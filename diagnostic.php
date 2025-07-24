<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Função de conexão igual ao api.php
function getDbConnection() {
    $database_url = getenv('DATABASE_URL');
    
    if (!$database_url) {
        $host = 'dpg-d1u47ber433s73ebqecg-a.oregon-postgres.render.com';
        $dbname = 'enedesifb';
        $username = 'enedesifb_user';
        $password = 'E8kOWf5R9eAUV6XZJBeYNVcgBdmcTJUB';
        $port = 5432;
        $database_url = "postgresql://$username:$password@$host:$port/$dbname";
    }
    
    $db = parse_url($database_url);
    $host = $db['host'] ?? 'localhost';
    $port = isset($db['port']) ? (int)$db['port'] : 5432;
    $dbname = isset($db['path']) ? ltrim($db['path'], '/') : 'enedesifb';
    $username = $db['user'] ?? '';
    $password = $db['pass'] ?? '';
    
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";
    
    return new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_TIMEOUT => 30
    ]);
}

function testInsertWithCorrectStructure($pdo) {
    $results = [];
    
    try {
        // 1. Verificar estrutura da tabela metas
        $stmt = $pdo->query("
            SELECT column_name 
            FROM information_schema.columns 
            WHERE table_name = 'metas' AND table_schema = 'public'
        ");
        $metas_columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $results['metas_columns'] = $metas_columns;
        
        // 2. Testar inserção usando as colunas corretas
        $has_title = in_array('title', $metas_columns);
        $has_titulo = in_array('titulo', $metas_columns);
        
        if ($has_title) {
            // Usar 'title' se existir
            $stmt = $pdo->prepare("
                INSERT INTO metas (title, objetivo, status) 
                VALUES (?, ?, ?) 
                RETURNING id
            ");
            $stmt->execute([
                'Teste de Diagnóstico - ' . date('H:i:s'),
                'Verificar funcionamento da API',
                'ativo'
            ]);
            $test_id = $stmt->fetchColumn();
            
            // Ler o registro
            $stmt = $pdo->prepare("SELECT * FROM metas WHERE id = ?");
            $stmt->execute([$test_id]);
            $test_record = $stmt->fetch();
            
            // Limpar teste
            $pdo->prepare("DELETE FROM metas WHERE id = ?")->execute([$test_id]);
            
            $results['test_with_title'] = [
                'status' => 'SUCCESS',
                'inserted_record' => $test_record
            ];
            
        } elseif ($has_titulo) {
            // Usar 'titulo' se 'title' não existir
            $stmt = $pdo->prepare("
                INSERT INTO metas (titulo, descricao, status) 
                VALUES (?, ?, ?) 
                RETURNING id
            ");
            $stmt->execute([
                'Teste de Diagnóstico - ' . date('H:i:s'),
                'Verificar funcionamento da API',
                'ativo'
            ]);
            $test_id = $stmt->fetchColumn();
            
            // Ler o registro
            $stmt = $pdo->prepare("SELECT * FROM metas WHERE id = ?");
            $stmt->execute([$test_id]);
            $test_record = $stmt->fetch();
            
            // Limpar teste
            $pdo->prepare("DELETE FROM metas WHERE id = ?")->execute([$test_id]);
            
            $results['test_with_titulo'] = [
                'status' => 'SUCCESS',
                'inserted_record' => $test_record
            ];
        } else {
            $results['test_insert'] = [
                'status' => 'FAILED',
                'error' => 'Nem title nem titulo encontrados na tabela metas'
            ];
        }
        
        // 3. Testar tabela actions
        $stmt = $pdo->query("
            SELECT column_name 
            FROM information_schema.columns 
            WHERE table_name = 'actions' AND table_schema = 'public'
        ");
        $actions_columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $results['actions_columns'] = $actions_columns;
        
        if (in_array('titulo', $actions_columns)) {
            $stmt = $pdo->prepare("
                INSERT INTO actions (titulo, descricao, status) 
                VALUES (?, ?, ?) 
                RETURNING id
            ");
            $stmt->execute([
                'Ação de Teste - ' . date('H:i:s'),
                'Verificar inserção em actions',
                'pending'
            ]);
            $test_id = $stmt->fetchColumn();
            
            // Ler e limpar
            $stmt = $pdo->prepare("SELECT * FROM actions WHERE id = ?");
            $stmt->execute([$test_id]);
            $test_record = $stmt->fetch();
            $pdo->prepare("DELETE FROM actions WHERE id = ?")->execute([$test_id]);
            
            $results['test_actions'] = [
                'status' => 'SUCCESS',
                'inserted_record' => $test_record
            ];
        }
        
    } catch (Exception $e) {
        $results['test_insert'] = [
            'status' => 'FAILED',
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ];
    }
    
    return $results;
}

function runDatabaseDiagnostic($pdo) {
    $diagnostic = [
        'timestamp' => date('Y-m-d H:i:s'),
        'connection_test' => null,
        'structure_analysis' => null,
        'insert_test' => null,
        'recommendations' => []
    ];
    
    try {
        // 1. Teste de conexão
        $stmt = $pdo->query("SELECT version(), current_database(), current_user");
        $connection_info = $stmt->fetch();
        $diagnostic['connection_test'] = [
            'status' => 'SUCCESS',
            'info' => $connection_info
        ];
        
        // 2. Análise de estrutura
        $diagnostic['structure_analysis'] = testInsertWithCorrectStructure($pdo);
        
        // 3. Contagem de registros
        $tables = ['metas', 'actions', 'cronograma', 'followups', 'tasks', 'inventario'];
        $counts = [];
        foreach ($tables as $table) {
            try {
                $stmt = $pdo->query("SELECT COUNT(*) FROM $table");
                $counts[$table] = $stmt->fetchColumn();
            } catch (Exception $e) {
                $counts[$table] = "ERROR: " . $e->getMessage();
            }
        }
        $diagnostic['table_counts'] = $counts;
        
        // 4. Recomendações
        $metas_columns = $diagnostic['structure_analysis']['metas_columns'] ?? [];
        
        if (in_array('title', $metas_columns) && !in_array('titulo', $metas_columns)) {
            $diagnostic['recommendations'][] = "Considere adicionar coluna 'titulo' na tabela metas para compatibilidade";
        }
        
        if (in_array('titulo', $metas_columns) && in_array('title', $metas_columns)) {
            $diagnostic['recommendations'][] = "✅ Tabela metas tem both 'title' e 'titulo' - compatibilidade total";
        }
        
        $diagnostic['overall_status'] = 'HEALTHY';
        
    } catch (Exception $e) {
        $diagnostic['connection_test'] = [
            'status' => 'FAILED',
            'error' => $e->getMessage()
        ];
        $diagnostic['overall_status'] = 'FAILED';
    }
    
    return $diagnostic;
}

// Executar baseado no parâmetro
try {
    $pdo = getDbConnection();
    
    $test = $_GET['test'] ?? '1';
    
    switch ($test) {
        case '1':
        case 'full':
            $result = runDatabaseDiagnostic($pdo);
            break;
            
        case 'structure':
            $result = testInsertWithCorrectStructure($pdo);
            break;
            
        case 'simple':
            $stmt = $pdo->query("SELECT 'Database OK' as status, NOW() as timestamp");
            $result = [
                'status' => 'SUCCESS',
                'data' => $stmt->fetch(),
                'message' => 'Conexão PostgreSQL funcionando'
            ];
            break;
            
        default:
            $result = ['error' => 'Parâmetro test inválido. Use: ?test=1, ?test=structure ou ?test=simple'];
    }
    
    echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'FAILED',
        'error' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
?>
