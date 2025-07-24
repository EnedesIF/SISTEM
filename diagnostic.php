<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

function logMessage($message) {
    error_log("[DIAGNOSTIC] " . $message);
    return $message;
}

function testDatabaseConnection() {
    $results = [];
    
    try {
        // 1. Verificar variáveis de ambiente
        $database_url = getenv('DATABASE_URL') ?: $_ENV['DATABASE_URL'] ?? null;
        $results['env_check'] = [
            'DATABASE_URL_exists' => !empty($database_url),
            'DATABASE_URL_preview' => $database_url ? substr($database_url, 0, 30) . '...' : 'NOT SET'
        ];
        
        // 2. Teste de conexão com fallback (igual ao api.php)
        if (!$database_url) {
            $host = 'dpg-d1u47ber433s73ebqecg-a.oregon-postgres.render.com';
            $dbname = 'enedesifb';
            $username = 'enedesifb_user';
            $password = 'E8kOWf5R9eAUV6XZJBeYNVcgBdmcTJUB';
            $port = '5432';
            $database_url = "postgresql://$username:$password@$host:$port/$dbname";
            $results['using_fallback'] = true;
        }
        
        $db = parse_url($database_url);
        $results['parsed_config'] = [
            'host' => $db['host'] ?? 'NOT_FOUND',
            'port' => $db['port'] ?? 'NOT_FOUND',
            'database' => ltrim($db['path'] ?? '', '/') ?: 'NOT_FOUND',
            'username' => $db['user'] ?? 'NOT_FOUND'
        ];
        
        // 3. Tentar conexão exatamente como no api.php
        $pdo = new PDO(
            "pgsql:host={$db['host']};port={$db['port']};dbname=" . ltrim($db['path'], '/'),
            $db['user'],
            $db['pass'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
        
        $results['connection'] = [
            'status' => 'SUCCESS',
            'message' => 'Conexão PostgreSQL estabelecida'
        ];
        
        // 4. Verificar versão do PostgreSQL
        $stmt = $pdo->query("SELECT version()");
        $version = $stmt->fetchColumn();
        $results['database_info'] = [
            'version' => $version,
            'current_database' => $pdo->query("SELECT current_database()")->fetchColumn(),
            'current_user' => $pdo->query("SELECT current_user")->fetchColumn()
        ];
        
        // 5. Verificar tabelas existentes
        $stmt = $pdo->query("
            SELECT table_name 
            FROM information_schema.tables 
            WHERE table_schema = 'public'
            ORDER BY table_name
        ");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $results['existing_tables'] = $tables;
        
        // 6. Teste de criação de tabela (como no api.php)
        try {
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS test_table_diagnostic (
                    id SERIAL PRIMARY KEY,
                    teste_campo VARCHAR(100),
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            ");
            
            // Inserir teste
            $stmt = $pdo->prepare("INSERT INTO test_table_diagnostic (teste_campo) VALUES (?) RETURNING id");
            $stmt->execute(['Teste diagnóstico - ' . date('Y-m-d H:i:s')]);
            $test_id = $stmt->fetchColumn();
            
            // Buscar teste
            $stmt = $pdo->prepare("SELECT * FROM test_table_diagnostic WHERE id = ?");
            $stmt->execute([$test_id]);
            $test_record = $stmt->fetch();
            
            // Limpar teste
            $stmt = $pdo->prepare("DELETE FROM test_table_diagnostic WHERE id = ?");
            $stmt->execute([$test_id]);
            
            // Dropar tabela de teste
            $pdo->exec("DROP TABLE IF EXISTS test_table_diagnostic");
            
            $results['crud_test'] = [
                'create_table' => 'SUCCESS',
                'insert' => 'SUCCESS', 
                'select' => 'SUCCESS',
                'delete' => 'SUCCESS',
                'drop_table' => 'SUCCESS',
                'test_record' => $test_record
            ];
            
        } catch (Exception $e) {
            $results['crud_test'] = [
                'status' => 'FAILED',
                'error' => $e->getMessage()
            ];
        }
        
        // 7. Verificar configurações do PostgreSQL
        $stmt = $pdo->query("SHOW all");
        $configs = $stmt->fetchAll();
        $important_configs = [];
        foreach ($configs as $config) {
            if (in_array($config['name'], ['autocommit', 'timezone', 'client_encoding', 'server_version'])) {
                $important_configs[$config['name']] = $config['setting'];
            }
        }
        $results['postgres_settings'] = $important_configs;
        
    } catch (Exception $e) {
        $results['connection'] = [
            'status' => 'FAILED',
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ];
    }
    
    return $results;
}

function checkFiles() {
    $files_to_check = [
        'api/api.php',
        'api/config.php', 
        'api/actions.php',
        'api/goals.php',
        'api/cronograma.php',
        'api/diagnostic.php'
    ];
    
    $file_status = [];
    
    foreach ($files_to_check as $file) {
        $full_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $file;
        $file_status[$file] = [
            'exists' => file_exists($full_path),
            'readable' => file_exists($full_path) && is_readable($full_path),
            'size' => file_exists($full_path) ? filesize($full_path) : 0,
            'modified' => file_exists($full_path) ? date('Y-m-d H:i:s', filemtime($full_path)) : 'N/A'
        ];
        
        // Verificar se o arquivo contém "autocommit" (causa do erro)
        if (file_exists($full_path)) {
            $content = file_get_contents($full_path);
            $file_status[$file]['contains_autocommit'] = strpos($content, 'autocommit') !== false;
            $file_status[$file]['contains_mysql'] = strpos(strtolower($content), 'mysql') !== false;
        }
    }
    
    return $file_status;
}

function testAPIEndpoints() {
    $base_url = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
    $endpoints = [
        'api_test' => $base_url . '/api/api.php?endpoint=test',
        'api_goals' => $base_url . '/api/api.php?endpoint=goals', 
        'api_actions' => $base_url . '/api/api.php?endpoint=actions'
    ];
    
    $results = [];
    
    foreach ($endpoints as $name => $url) {
        try {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 15,
                    'method' => 'GET',
                    'header' => "Content-Type: application/json\r\n"
                ]
            ]);
            
            $response = @file_get_contents($url, false, $context);
            $http_code = 200;
            
            if ($response === false) {
                $error = error_get_last();
                $results[$name] = [
                    'url' => $url,
                    'status' => 'FAILED',
                    'error' => $error['message'] ?? 'Unknown error'
                ];
            } else {
                $json_response = json_decode($response, true);
                $results[$name] = [
                    'url' => $url,
                    'status' => 'SUCCESS',
                    'response_length' => strlen($response),
                    'is_json' => $json_response !== null,
                    'response_preview' => substr($response, 0, 200) . '...'
                ];
            }
        } catch (Exception $e) {
            $results[$name] = [
                'url' => $url,
                'status' => 'EXCEPTION',
                'error' => $e->getMessage()
            ];
        }
    }
    
    return $results;
}

function getSystemInfo() {
    return [
        'php_version' => PHP_VERSION,
        'extensions' => [
            'pdo' => extension_loaded('pdo'),
            'pdo_pgsql' => extension_loaded('pdo_pgsql'),
            'curl' => extension_loaded('curl'),
            'json' => extension_loaded('json')
        ],
        'server_info' => [
            'software' => $_SERVER['SERVER_SOFTWARE'] ?? 'UNKNOWN',
            'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'UNKNOWN',
            'script_filename' => $_SERVER['SCRIPT_FILENAME'] ?? 'UNKNOWN',
            'http_host' => $_SERVER['HTTP_HOST'] ?? 'UNKNOWN'
        ],
        'time_info' => [
            'current_time' => date('Y-m-d H:i:s'),
            'timezone' => date_default_timezone_get(),
            'server_time' => $_SERVER['REQUEST_TIME'] ?? time()
        ],
        'memory_info' => [
            'memory_limit' => ini_get('memory_limit'),
            'memory_usage' => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true)
        ]
    ];
}

// Executar diagnóstico completo
$diagnostic = [
    'timestamp' => date('Y-m-d H:i:s'),
    'diagnostic_version' => '2.0',
    'system_info' => getSystemInfo(),
    'file_check' => checkFiles(),
    'database_test' => testDatabaseConnection(),
    'api_endpoints_test' => testAPIEndpoints()
];

// Determinar status geral
$overall_status = 'UNKNOWN';
$issues = [];

// Verificar problemas
if (isset($diagnostic['database_test']['connection']['status'])) {
    if ($diagnostic['database_test']['connection']['status'] === 'SUCCESS') {
        $overall_status = 'HEALTHY';
    } else {
        $overall_status = 'DATABASE_ERROR';
        $issues[] = 'Database connection failed: ' . $diagnostic['database_test']['connection']['error'];
    }
} else {
    $overall_status = 'CONFIGURATION_ERROR';
    $issues[] = 'Database configuration error';
}

// Verificar arquivos com autocommit
foreach ($diagnostic['file_check'] as $file => $info) {
    if (isset($info['contains_autocommit']) && $info['contains_autocommit']) {
        $issues[] = "File $file contains 'autocommit' - this causes PostgreSQL errors";
    }
}

$diagnostic['overall_status'] = $overall_status;
$diagnostic['issues_found'] = $issues;
$diagnostic['recommendations'] = [];

if ($overall_status === 'HEALTHY') {
    $diagnostic['recommendations'][] = '✅ Sistema funcionando corretamente';
} else {
    $diagnostic['recommendations'][] = '❌ Problemas encontrados - verifique os issues_found';
    if (!empty($issues)) {
        foreach ($issues as $issue) {
            $diagnostic['recommendations'][] = "🔧 Corrigir: $issue";
        }
    }
}

// Log do diagnóstico
logMessage("Diagnostic completed with status: $overall_status");
if (!empty($issues)) {
    logMessage("Issues found: " . implode('; ', $issues));
}

echo json_encode($diagnostic, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
