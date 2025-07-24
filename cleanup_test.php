<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Script para limpar cache e testar sistema completamente
error_reporting(E_ALL);
ini_set('display_errors', 1);

function clearPHPCache() {
    $results = [];
    
    // Limpar cache do OPcache se disponível
    if (function_exists('opcache_reset')) {
        opcache_reset();
        $results['opcache'] = 'CLEARED';
    } else {
        $results['opcache'] = 'NOT_AVAILABLE';
    }
    
    // Limpar cache de realpath
    if (function_exists('clearstatcache')) {
        clearstatcache();
        $results['statcache'] = 'CLEARED';
    }
    
    return $results;
}

function testDirectConnection() {
    try {
        // Conexão direta usando as credenciais exatas
        $host = 'dpg-d1u47ber433s73ebqecg-a.oregon-postgres.render.com';
        $dbname = 'enedesifb';
        $username = 'enedesifb_user';
        $password = 'E8kOWf5R9eAUV6XZJBeYNVcgBdmcTJUB';
        $port = '5432';
        
        // DSN limpo e simples
        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";
        
        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_TIMEOUT => 20
        ]);
        
        // Teste básico
        $stmt = $pdo->query("SELECT 'Direct connection working!' as test, NOW() as timestamp");
        $result = $stmt->fetch();
        
        // Teste de criação de tabela
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS test_cleanup (
                id SERIAL PRIMARY KEY,
                message TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        // Inserir teste
        $stmt = $pdo->prepare("INSERT INTO test_cleanup (message) VALUES (?) RETURNING id");
        $stmt->execute(['Cleanup test - ' . date('Y-m-d H:i:s')]);
        $test_id = $stmt->fetchColumn();
        
        // Ler teste
        $stmt = $pdo->prepare("SELECT * FROM test_cleanup WHERE id = ?");
        $stmt->execute([$test_id]);
        $test_record = $stmt->fetch();
        
        // Limpar teste
        $pdo->exec("DROP TABLE IF EXISTS test_cleanup");
        
        return [
            'status' => 'SUCCESS',
            'connection_test' => $result,
            'crud_test' => $test_record,
            'message' => 'Conexão direta PostgreSQL funcionando perfeitamente'
        ];
        
    } catch (Exception $e) {
        return [
            'status' => 'FAILED',
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ];
    }
}

function testEnvironmentVariable() {
    $database_url = getenv('DATABASE_URL') ?: $_ENV['DATABASE_URL'] ?? null;
    
    $result = [
        'env_var_exists' => !empty($database_url),
        'env_var_preview' => $database_url ? substr($database_url, 0, 30) . '...' : 'NOT_SET'
    ];
    
    if ($database_url) {
        try {
            $db = parse_url($database_url);
            $dsn = "pgsql:host={$db['host']};port={$db['port']};dbname=" . ltrim($db['path'], '/');
            $pdo = new PDO($dsn, $db['user'], $db['pass'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            
            $stmt = $pdo->query("SELECT 'Environment variable working!' as test");
            $test_result = $stmt->fetch();
            
            $result['env_test'] = [
                'status' => 'SUCCESS',
                'result' => $test_result
            ];
            
        } catch (Exception $e) {
            $result['env_test'] = [
                'status' => 'FAILED', 
                'error' => $e->getMessage()
            ];
        }
    }
    
    return $result;
}

function runCompleteTest() {
    $start_time = microtime(true);
    
    $results = [
        'timestamp' => date('Y-m-d H:i:s'),
        'test_version' => '1.0',
        'cache_cleanup' => clearPHPCache(),
        'environment_test' => testEnvironmentVariable(),
        'direct_connection_test' => testDirectConnection()
    ];
    
    // Determinar status geral
    $overall_success = true;
    $issues = [];
    
    if ($results['direct_connection_test']['status'] !== 'SUCCESS') {
        $overall_success = false;
        $issues[] = 'Direct PostgreSQL connection failed';
    }
    
    if (isset($results['environment_test']['env_test']) && 
        $results['environment_test']['env_test']['status'] !== 'SUCCESS') {
        $issues[] = 'Environment variable DATABASE_URL has issues';
    }
    
    $results['overall_status'] = $overall_success ? 'SUCCESS' : 'FAILED';
    $results['issues'] = $issues;
    $results['execution_time'] = round((microtime(true) - $start_time) * 1000, 2) . 'ms';
    
    return $results;
}

// Verificar se é uma requisição de teste específica
$test_type = $_GET['test'] ?? 'complete';

switch ($test_type) {
    case 'direct':
        echo json_encode(testDirectConnection(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        break;
        
    case 'env':
        echo json_encode(testEnvironmentVariable(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        break;
        
    case 'cache':
        echo json_encode(clearPHPCache(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        break;
        
    case 'complete':
    default:
        echo json_encode(runCompleteTest(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        break;
}
?>
