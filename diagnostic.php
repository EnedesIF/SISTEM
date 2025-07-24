<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$diagnostic = [
    'timestamp' => date('Y-m-d H:i:s'),
    'server_info' => [
        'php_version' => phpversion(),
        'current_dir' => getcwd(),
        'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'Not set',
        'server_name' => $_SERVER['SERVER_NAME'] ?? 'Not set'
    ],
    'environment' => [],
    'database' => [
        'status' => 'not_tested',
        'connection' => null,
        'tables' => []
    ],
    'files' => [
        'index.html' => file_exists('index.html'),
        'api.php' => file_exists('api.php'),
        'render.yaml' => file_exists('render.yaml'),
        'Dockerfile' => file_exists('Dockerfile')
    ]
];

// Verificar variáveis de ambiente
$env_vars = ['DATABASE_URL', 'PHP_VERSION'];
foreach ($env_vars as $var) {
    $diagnostic['environment'][$var] = getenv($var) ?: 'Not set';
}

// Testar conexão com banco
try {
    $database_url = getenv('DATABASE_URL');
    if ($database_url) {
        $db_parts = parse_url($database_url);
        
        $host = $db_parts['host'];
        $port = $db_parts['port'];
        $user = $db_parts['user'];
        $pass = $db_parts['pass'];
        $dbname = ltrim($db_parts['path'], '/');
        
        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        
        $diagnostic['database']['status'] = 'connected';
        $diagnostic['database']['connection'] = [
            'host' => $host,
            'port' => $port,
            'database' => $dbname,
            'user' => $user
        ];
        
        // Verificar tabelas existentes
        $stmt = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $diagnostic['database']['tables'] = $tables;
        
        // Verificar estrutura da tabela metas se existir
        if (in_array('metas', $tables)) {
            $stmt = $pdo->query("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'metas' ORDER BY ordinal_position");
            $columns = $stmt->fetchAll();
            $diagnostic['database']['metas_structure'] = $columns;
        }
        
    } else {
        $diagnostic['database']['status'] = 'no_database_url';
    }
} catch (Exception $e) {
    $diagnostic['database']['status'] = 'error';
    $diagnostic['database']['error'] = $e->getMessage();
}

// Verificar se é requisição AJAX
if (isset($_GET['format']) && $_GET['format'] === 'html') {
    header('Content-Type: text/html');
    echo "<!DOCTYPE html>
<html>
<head>
    <title>ENEDES System Diagnostic</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .success { background: #d4edda; border-color: #c3e6cb; }
        .error { background: #f8d7da; border-color: #f5c6cb; }
        .warning { background: #fff3cd; border-color: #ffeaa7; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 3px; overflow-x: auto; }
        h2 { color: #333; margin-top: 0; }
    </style>
</head>
<body>
    <h1>🔍 ENEDES System Diagnostic</h1>";
    
    echo "<div class='section " . ($diagnostic['database']['status'] === 'connected' ? 'success' : 'error') . "'>";
    echo "<h2>Database Status: " . $diagnostic['database']['status'] . "</h2>";
    if ($diagnostic['database']['status'] === 'connected') {
        echo "<p>✅ Connected successfully</p>";
        echo "<p><strong>Tables found:</strong> " . implode(', ', $diagnostic['database']['tables']) . "</p>";
    } else {
        echo "<p>❌ Connection failed</p>";
        if (isset($diagnostic['database']['error'])) {
            echo "<p><strong>Error:</strong> " . $diagnostic['database']['error'] . "</p>";
        }
    }
    echo "</div>";
    
    echo "<div class='section'>";
    echo "<h2>Files Status</h2>";
    foreach ($diagnostic['files'] as $file => $exists) {
        echo "<p>" . ($exists ? "✅" : "❌") . " $file</p>";
    }
    echo "</div>";
    
    echo "<div class='section'>";
    echo "<h2>Raw Diagnostic Data</h2>";
    echo "<pre>" . json_encode($diagnostic, JSON_PRETTY_PRINT) . "</pre>";
    echo "</div>";
    
    echo "</body></html>";
} else {
    echo json_encode($diagnostic, JSON_PRETTY_PRINT);
}
?>
