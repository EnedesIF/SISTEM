<?php
// api.php - Backend API para Sistema ENEDES - CORRIGIDO para PostgreSQL
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration - PostgreSQL Render - CORRIGIDO
function getDbConnection() {
    $database_url = getenv('DATABASE_URL');
    
    if (!$database_url) {
        // Fallback configuration - suas credenciais Render PostgreSQL
        $host = 'dpg-d1u47ber433s73ebqecg-a.oregon-postgres.render.com';
        $dbname = 'enedesifb';
        $username = 'enedesifb_user';
        $password = 'E8kOWf5R9eAUV6XZJBeYNVcgBdmcTJUB';
        $port = 5432;
        
        $database_url = "postgresql://$username:$password@$host:$port/$dbname";
    }
    
    try {
        $db = parse_url($database_url);
        
        // ✅ CORREÇÃO: Verificar se as chaves existem antes de usar
        $host = $db['host'] ?? 'localhost';
        $port = isset($db['port']) ? (int)$db['port'] : 5432; // ✅ CAST para int e default
        $dbname = isset($db['path']) ? ltrim($db['path'], '/') : 'enedesifb';
        $username = $db['user'] ?? '';
        $password = $db['pass'] ?? '';
        
        // ✅ DSN corrigido com verificações
        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";
        
        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_TIMEOUT => 30
        ]);
        
        // ✅ REMOVIDO: autocommit (PostgreSQL não suporta)
        // PostgreSQL usa autocommit por padrão - não precisa configurar
        
        // Create tables if they don't exist
        createTablesIfNotExist($pdo);
        
        return $pdo;
    } catch (PDOException $e) {
        error_log("Database connection error: " . $e->getMessage());
        throw new Exception("Database connection failed: " . $e->getMessage());
    }
}

// Create necessary tables - Otimizado para PostgreSQL
function createTablesIfNotExist($pdo) {
    $tables = [
        'metas' => '
            CREATE TABLE IF NOT EXISTS metas (
                id SERIAL PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                objetivo TEXT,
                program VARCHAR(255),
                indicadores JSONB DEFAULT \'[]\',
                status VARCHAR(50) DEFAULT \'ativo\',
                created_by VARCHAR(255),
                created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
            )',
        'actions' => '
            CREATE TABLE IF NOT EXISTS actions (
                id SERIAL PRIMARY KEY,
                titulo VARCHAR(255) NOT NULL,
                programa VARCHAR(255),
                descricao TEXT,
                responsavel VARCHAR(255),
                status VARCHAR(50) DEFAULT \'pending\',
                created_by VARCHAR(255),
                created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
            )',
        'followups' => '
            CREATE TABLE IF NOT EXISTS followups (
                id SERIAL PRIMARY KEY,
                target_id INTEGER,
                type VARCHAR(50),
                mensagem TEXT,
                prioridade VARCHAR(50) DEFAULT \'media\',
                prazo DATE,
                colaboradores JSONB DEFAULT \'[]\',
                attachments JSONB DEFAULT \'[]\',
                status VARCHAR(50) DEFAULT \'pending\',
                created_by VARCHAR(255),
                created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
            )',
        'tasks' => '
            CREATE TABLE IF NOT EXISTS tasks (
                id SERIAL PRIMARY KEY,
                followup_id INTEGER,
                titulo VARCHAR(255) NOT NULL,
                descricao TEXT,
                responsavel VARCHAR(255),
                status VARCHAR(50) DEFAULT \'pending\',
                prazo DATE,
                attachments JSONB DEFAULT \'[]\',
                created_by VARCHAR(255),
                created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
            )',
        'cronograma' => '
            CREATE TABLE IF NOT EXISTS cronograma (
                id SERIAL PRIMARY KEY,
                nome VARCHAR(255) NOT NULL,
                inicio DATE,
                fim DATE,
                rubrica DECIMAL(15,2) DEFAULT 0,
                executado DECIMAL(15,2) DEFAULT 0,
                created_by VARCHAR(255),
                created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
            )',
        'inventario' => '
            CREATE TABLE IF NOT EXISTS inventario (
                id SERIAL PRIMARY KEY,
                programa VARCHAR(255),
                item VARCHAR(255) NOT NULL,
                descricao TEXT,
                valor DECIMAL(15,2) DEFAULT 0,
                atividades_relacionadas TEXT,
                created_by VARCHAR(255),
                created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
            )'
    ];
    
    foreach ($tables as $table => $sql) {
        try {
            $pdo->exec($sql);
        } catch (PDOException $e) {
            error_log("Error creating table $table: " . $e->getMessage());
        }
    }
}

// Main router
try {
    $endpoint = $_GET['endpoint'] ?? '';
    $method = $_SERVER['REQUEST_METHOD'];
    $pdo = getDbConnection();
    
    switch ($endpoint) {
        case 'test':
            // Test database connection and show detailed info
            try {
                // Test basic query
                $stmt = $pdo->query("SELECT 1 as test_number, NOW() as current_time, version() as pg_version");
                $test_result = $stmt->fetch();
                
                // Count existing data for test response
                $metasCount = $pdo->query('SELECT COUNT(*) FROM metas')->fetchColumn();
                $actionsCount = $pdo->query('SELECT COUNT(*) FROM actions')->fetchColumn();
                
                // Test insert/delete to verify write permissions
                $pdo->exec("CREATE TABLE IF NOT EXISTS test_connection (id SERIAL PRIMARY KEY, test_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");
                $stmt = $pdo->prepare("INSERT INTO test_connection DEFAULT VALUES RETURNING id");
                $stmt->execute();
                $test_id = $stmt->fetchColumn();
                $pdo->exec("DELETE FROM test_connection WHERE id = $test_id");
                $pdo->exec("DROP TABLE IF EXISTS test_connection");
                
                echo json_encode([
                    'status' => 'success',
                    'message' => 'ENEDES API funcionando perfeitamente com PostgreSQL!',
                    'timestamp' => date('Y-m-d H:i:s'),
                    'method' => $method,
                    'php_version' => PHP_VERSION,
                    'database_connected' => true,
                    'database_test' => $test_result,
                    'config_method' => getenv('DATABASE_URL') ? 'Environment Variable (Secure)' : 'Fallback Config',
                    'environment' => 'Production - Render',
                    'database_type' => 'PostgreSQL',
                    'render_connected' => 'Yes',
                    'tables_auto_created' => 'Yes',
                    'write_test' => 'Success',
                    'current_data' => [
                        'metas_count' => (int)$metasCount,
                        'actions_count' => (int)$actionsCount
                    ]
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            } catch (Exception $e) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Test failed: ' . $e->getMessage(),
                    'timestamp' => date('Y-m-d H:i:s')
                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            }
            break;
            
        case 'goals':
        case 'metas':
            handleGoals($pdo, $method);
            break;
            
        case 'actions':
            handleActions($pdo, $method);
            break;
            
        case 'followups':
            handleFollowups($pdo, $method);
            break;
            
        case 'tasks':
            handleTasks($pdo, $method);
            break;
            
        case 'cronograma':
        case 'schedule':
            handleCronograma($pdo, $method);
            break;
            
        case 'inventory':
        case 'inventario':
            handleInventario($pdo, $method);
            break;
            
        default:
            http_response_code(404);
            echo json_encode([
                'error' => 'Endpoint not found',
                'available_endpoints' => [
                    'test', 'goals', 'actions', 'followups', 
                    'tasks', 'cronograma', 'inventory'
                ],
                'usage' => 'Add ?endpoint=ENDPOINT_NAME to your request'
            ]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    error_log("API Fatal Error: " . $e->getMessage());
    echo json_encode([
        'error' => 'Internal server error',
        'message' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s'),
        'debug_info' => [
            'line' => $e->getLine(),
            'file' => basename($e->getFile())
        ]
    ]);
}

// Handler functions
function handleGoals($pdo, $method) {
    switch ($method) {
        case 'GET':
            $stmt = $pdo->query('SELECT * FROM metas ORDER BY created_at DESC');
            $metas = $stmt->fetchAll();
            
            // Decode JSON fields
            foreach ($metas as &$meta) {
                if (isset($meta['indicadores'])) {
                    $meta['indicadores'] = json_decode($meta['indicadores'], true) ?: [];
                }
            }
            
            echo json_encode(['success' => true, 'data' => $metas]);
            break;
            
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || !isset($input['title'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing required field: title']);
                return;
            }
            
            $stmt = $pdo->prepare('
                INSERT INTO metas (title, objetivo, program, indicadores, status, created_by) 
                VALUES (?, ?, ?, ?, ?, ?) RETURNING id
            ');
            
            $stmt->execute([
                $input['title'],
                $input['objetivo'] ?? '',
                $input['program'] ?? '',
                json_encode($input['indicadores'] ?? []),
                $input['status'] ?? 'ativo',
                $input['created_by'] ?? 'Unknown'
            ]);
            
            $id = $stmt->fetchColumn();
            echo json_encode(['success' => true, 'id' => $id]);
            break;
            
        case 'PUT':
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || !isset($input['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing required field: id']);
                return;
            }
            
            $stmt = $pdo->prepare('
                UPDATE metas SET title=?, objetivo=?, program=?, indicadores=?, status=?, updated_at=CURRENT_TIMESTAMP 
                WHERE id=?
            ');
            
            $stmt->execute([
                $input['title'],
                $input['objetivo'] ?? '',
                $input['program'] ?? '',
                json_encode($input['indicadores'] ?? []),
                $input['status'] ?? 'ativo',
                $input['id']
            ]);
            
            echo json_encode(['success' => true]);
            break;
            
        case 'DELETE':
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || !isset($input['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing required field: id']);
                return;
            }
            
            $stmt = $pdo->prepare('DELETE FROM metas WHERE id = ?');
            $stmt->execute([$input['id']]);
            
            echo json_encode(['success' => true]);
            break;
    }
}

function handleActions($pdo, $method) {
    switch ($method) {
        case 'GET':
            $stmt = $pdo->query('SELECT * FROM actions ORDER BY created_at DESC');
            $actions = $stmt->fetchAll();
            echo json_encode(['success' => true, 'data' => $actions]);
            break;
            
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || !isset($input['titulo'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing required field: titulo']);
                return;
            }
            
            $stmt = $pdo->prepare('
                INSERT INTO actions (titulo, programa, descricao, responsavel, status, created_by) 
                VALUES (?, ?, ?, ?, ?, ?) RETURNING id
            ');
            
            $stmt->execute([
                $input['titulo'],
                $input['programa'] ?? '',
                $input['descricao'] ?? '',
                $input['responsavel'] ?? '',
                $input['status'] ?? 'pending',
                $input['created_by'] ?? 'Unknown'
            ]);
            
            $id = $stmt->fetchColumn();
            echo json_encode(['success' => true, 'id' => $id]);
            break;
            
        case 'PUT':
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || !isset($input['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing required field: id']);
                return;
            }
            
            $stmt = $pdo->prepare('
                UPDATE actions SET titulo=?, programa=?, descricao=?, responsavel=?, status=?, updated_at=CURRENT_TIMESTAMP 
                WHERE id=?
            ');
            
            $stmt->execute([
                $input['titulo'],
                $input['programa'] ?? '',
                $input['descricao'] ?? '',
                $input['responsavel'] ?? '',
                $input['status'] ?? 'pending',
                $input['id']
            ]);
            
            echo json_encode(['success' => true]);
            break;
            
        case 'DELETE':
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || !isset($input['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing required field: id']);
                return;
            }
            
            $stmt = $pdo->prepare('DELETE FROM actions WHERE id = ?');
            $stmt->execute([$input['id']]);
            
            echo json_encode(['success' => true]);
            break;
    }
}

function handleFollowups($pdo, $method) {
    switch ($method) {
        case 'GET':
            $stmt = $pdo->query('SELECT * FROM followups ORDER BY created_at DESC');
            $followups = $stmt->fetchAll();
            
            // Decode JSON fields
            foreach ($followups as &$followup) {
                if (isset($followup['colaboradores'])) {
                    $followup['colaboradores'] = json_decode($followup['colaboradores'], true) ?: [];
                }
                if (isset($followup['attachments'])) {
                    $followup['attachments'] = json_decode($followup['attachments'], true) ?: [];
                }
            }
            
            echo json_encode(['success' => true, 'data' => $followups]);
            break;
            
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            
            $stmt = $pdo->prepare('
                INSERT INTO followups (target_id, type, mensagem, prioridade, prazo, colaboradores, attachments, status, created_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?) RETURNING id
            ');
            
            $stmt->execute([
                $input['target_id'] ?? null,
                $input['type'] ?? '',
                $input['mensagem'] ?? '',
                $input['prioridade'] ?? 'media',
                $input['prazo'] ?? null,
                json_encode($input['colaboradores'] ?? []),
                json_encode($input['attachments'] ?? []),
                $input['status'] ?? 'pending',
                $input['created_by'] ?? 'Unknown'
            ]);
            
            $id = $stmt->fetchColumn();
            echo json_encode(['success' => true, 'id' => $id]);
            break;
            
        case 'PUT':
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || !isset($input['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing required field: id']);
                return;
            }
            
            $stmt = $pdo->prepare('
                UPDATE followups SET target_id=?, type=?, mensagem=?, prioridade=?, prazo=?, colaboradores=?, attachments=?, status=? 
                WHERE id=?
            ');
            
            $stmt->execute([
                $input['target_id'] ?? null,
                $input['type'] ?? '',
                $input['mensagem'] ?? '',
                $input['prioridade'] ?? 'media',
                $input['prazo'] ?? null,
                json_encode($input['colaboradores'] ?? []),
                json_encode($input['attachments'] ?? []),
                $input['status'] ?? 'pending',
                $input['id']
            ]);
            
            echo json_encode(['success' => true]);
            break;
            
        case 'DELETE':
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || !isset($input['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing required field: id']);
                return;
            }
            
            $stmt = $pdo->prepare('DELETE FROM followups WHERE id = ?');
            $stmt->execute([$input['id']]);
            
            echo json_encode(['success' => true]);
            break;
    }
}

function handleTasks($pdo, $method) {
    switch ($method) {
        case 'GET':
            $stmt = $pdo->query('SELECT * FROM tasks ORDER BY created_at DESC');
            $tasks = $stmt->fetchAll();
            
            // Decode JSON fields
            foreach ($tasks as &$task) {
                if (isset($task['attachments'])) {
                    $task['attachments'] = json_decode($task['attachments'], true) ?: [];
                }
            }
            
            echo json_encode(['success' => true, 'data' => $tasks]);
            break;
            
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || !isset($input['titulo'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing required field: titulo']);
                return;
            }
            
            $stmt = $pdo->prepare('
                INSERT INTO tasks (followup_id, titulo, descricao, responsavel, status, prazo, attachments, created_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?) RETURNING id
            ');
            
            $stmt->execute([
                $input['followup_id'] ?? null,
                $input['titulo'],
                $input['descricao'] ?? '',
                $input['responsavel'] ?? '',
                $input['status'] ?? 'pending',
                $input['prazo'] ?? null,
                json_encode($input['attachments'] ?? []),
                $input['created_by'] ?? 'Unknown'
            ]);
            
            $id = $stmt->fetchColumn();
            echo json_encode(['success' => true, 'id' => $id]);
            break;
            
        case 'PUT':
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || !isset($input['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing required field: id']);
                return;
            }
            
            $stmt = $pdo->prepare('
                UPDATE tasks SET followup_id=?, titulo=?, descricao=?, responsavel=?, status=?, prazo=?, attachments=? 
                WHERE id=?
            ');
            
            $stmt->execute([
                $input['followup_id'] ?? null,
                $input['titulo'],
                $input['descricao'] ?? '',
                $input['responsavel'] ?? '',
                $input['status'] ?? 'pending',
                $input['prazo'] ?? null,
                json_encode($input['attachments'] ?? []),
                $input['id']
            ]);
            
            echo json_encode(['success' => true]);
            break;
            
        case 'DELETE':
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || !isset($input['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing required field: id']);
                return;
            }
            
            $stmt = $pdo->prepare('DELETE FROM tasks WHERE id = ?');
            $stmt->execute([$input['id']]);
            
            echo json_encode(['success' => true]);
            break;
    }
}

function handleCronograma($pdo, $method) {
    switch ($method) {
        case 'GET':
            $stmt = $pdo->query('SELECT * FROM cronograma ORDER BY created_at DESC');
            $cronograma = $stmt->fetchAll();
            echo json_encode(['success' => true, 'data' => $cronograma]);
            break;
            
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || !isset($input['nome'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing required field: nome']);
                return;
            }
            
            $stmt = $pdo->prepare('
                INSERT INTO cronograma (nome, inicio, fim, rubrica, executado, created_by) 
                VALUES (?, ?, ?, ?, ?, ?) RETURNING id
            ');
            
            $stmt->execute([
                $input['nome'],
                $input['inicio'] ?? null,
                $input['fim'] ?? null,
                $input['rubrica'] ?? 0,
                $input['executado'] ?? 0,
                $input['created_by'] ?? 'Unknown'
            ]);
            
            $id = $stmt->fetchColumn();
            echo json_encode(['success' => true, 'id' => $id]);
            break;
            
        case 'PUT':
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || !isset($input['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing required field: id']);
                return;
            }
            
            $stmt = $pdo->prepare('
                UPDATE cronograma SET nome=?, inicio=?, fim=?, rubrica=?, executado=? 
                WHERE id=?
            ');
            
            $stmt->execute([
                $input['nome'],
                $input['inicio'] ?? null,
                $input['fim'] ?? null,
                $input['rubrica'] ?? 0,
                $input['executado'] ?? 0,
                $input['id']
            ]);
            
            echo json_encode(['success' => true]);
            break;
            
        case 'DELETE':
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || !isset($input['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing required field: id']);
                return;
            }
            
            $stmt = $pdo->prepare('DELETE FROM cronograma WHERE id = ?');
            $stmt->execute([$input['id']]);
            
            echo json_encode(['success' => true]);
            break;
    }
}

function handleInventario($pdo, $method) {
    switch ($method) {
        case 'GET':
            $stmt = $pdo->query('SELECT * FROM inventario ORDER BY created_at DESC');
            $inventario = $stmt->fetchAll();
            echo json_encode(['success' => true, 'data' => $inventario]);
            break;
            
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || !isset($input['item'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing required field: item']);
                return;
            }
            
            $stmt = $pdo->prepare('
                INSERT INTO inventario (programa, item, descricao, valor, atividades_relacionadas, created_by) 
                VALUES (?, ?, ?, ?, ?, ?) RETURNING id
            ');
            
            $stmt->execute([
                $input['programa'] ?? '',
                $input['item'],
                $input['descricao'] ?? '',
                $input['valor'] ?? 0,
                $input['atividades_relacionadas'] ?? '',
                $input['created_by'] ?? 'Unknown'
            ]);
            
            $id = $stmt->fetchColumn();
            echo json_encode(['success' => true, 'id' => $id]);
            break;
            
        case 'PUT':
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || !isset($input['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing required field: id']);
                return;
            }
            
            $stmt = $pdo->prepare('
                UPDATE inventario SET programa=?, item=?, descricao=?, valor=?, atividades_relacionadas=? 
                WHERE id=?
            ');
            
            $stmt->execute([
                $input['programa'] ?? '',
                $input['item'],
                $input['descricao'] ?? '',
                $input['valor'] ?? 0,
                $input['atividades_relacionadas'] ?? '',
                $input['id']
            ]);
            
            echo json_encode(['success' => true]);
            break;
            
        case 'DELETE':
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || !isset($input['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing required field: id']);
                return;
            }
            
            $stmt = $pdo->prepare('DELETE FROM inventario WHERE id = ?');
            $stmt->execute([$input['id']]);
            
            echo json_encode(['success' => true]);
            break;
    }
}
?>
