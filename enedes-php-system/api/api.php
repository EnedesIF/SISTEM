<?php
// api/api.php - Backend API para Sistema ENEDES
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Database configuration - Updated for new Neon database
function getDbConnection() {
    $database_url = getenv('DATABASE_URL');
    
    if (!$database_url) {
        // Fallback configuration based on your Neon dashboard
        $host = 'dpg-d1u47ber433s73ebqecg-a.oregon-postgres.render.com';
        $dbname = 'enedesifb';
        $username = 'enedesifb_user';
        $password = 'E8kOWf5R9eAUV6XZJBeYNVcgBdmcTJUB';
        $port = '5432';
        
        $database_url = "postgresql://$username:$password@$host:$port/$dbname";
    }
    
    try {
        $db = parse_url($database_url);
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
        
        // Create tables if they don't exist
        createTablesIfNotExist($pdo);
        
        return $pdo;
    } catch (PDOException $e) {
        throw new Exception("Database connection failed: " . $e->getMessage());
    }
}

// Create necessary tables
function createTablesIfNotExist($pdo) {
    $tables = [
        'metas' => '
            CREATE TABLE IF NOT EXISTS metas (
                id SERIAL PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                objetivo TEXT,
                program VARCHAR(255),
                indicadores JSON,
                status VARCHAR(50) DEFAULT \'ativo\',
                created_by VARCHAR(255),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
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
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )',
        'followups' => '
            CREATE TABLE IF NOT EXISTS followups (
                id SERIAL PRIMARY KEY,
                target_id INTEGER,
                type VARCHAR(50),
                mensagem TEXT,
                prioridade VARCHAR(50),
                prazo DATE,
                colaboradores JSON,
                attachments JSON,
                status VARCHAR(50) DEFAULT \'pending\',
                created_by VARCHAR(255),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )',
        'tasks' => '
            CREATE TABLE IF NOT EXISTS tasks (
                id SERIAL PRIMARY KEY,
                followup_id INTEGER,
                titulo VARCHAR(255),
                descricao TEXT,
                responsavel VARCHAR(255),
                status VARCHAR(50) DEFAULT \'pending\',
                prazo DATE,
                attachments JSON,
                created_by VARCHAR(255),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )',
        'cronograma' => '
            CREATE TABLE IF NOT EXISTS cronograma (
                id SERIAL PRIMARY KEY,
                nome VARCHAR(255),
                inicio DATE,
                fim DATE,
                rubrica DECIMAL(12,2),
                executado DECIMAL(12,2),
                created_by VARCHAR(255),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )',
        'inventario' => '
            CREATE TABLE IF NOT EXISTS inventario (
                id SERIAL PRIMARY KEY,
                programa VARCHAR(255),
                item VARCHAR(255),
                descricao TEXT,
                valor DECIMAL(12,2),
                atividades_relacionadas TEXT,
                created_by VARCHAR(255),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
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
            echo json_encode([
                'status' => 'success',
                'message' => 'ENEDES API funcionando perfeitamente!',
                'timestamp' => date('Y-m-d H:i:s'),
                'method' => $method,
                'php_version' => PHP_VERSION,
                'database_connected' => true,
                'config_method' => getenv('DATABASE_URL') ? 'Environment Variable (Secure)' : 'Fallback Config',
                'environment' => 'Production',
                'neon_connected' => 'Yes',
                'tables_auto_created' => 'Yes'
            ]);
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
            echo json_encode(['error' => 'Endpoint not found']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Internal server error',
        'message' => $e->getMessage()
    ]);
}

// Handler functions
function handleGoals($pdo, $method) {
    switch ($method) {
        case 'GET':
            $stmt = $pdo->query('SELECT * FROM metas ORDER BY created_at DESC');
            $metas = $stmt->fetchAll();
            echo json_encode(['success' => true, 'data' => $metas]);
            break;
            
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            $stmt = $pdo->prepare('
                INSERT INTO metas (title, objetivo, program, indicadores, status, created_by, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ');
            $stmt->execute([
                $input['title'],
                $input['objetivo'] ?? '',
                $input['program'],
                json_encode($input['indicadores'] ?? []),
                $input['status'] ?? 'ativo',
                $input['created_by'] ?? 'Unknown'
            ]);
            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
            break;
            
        case 'PUT':
            $input = json_decode(file_get_contents('php://input'), true);
            $id = $input['id'];
            $stmt = $pdo->prepare('
                UPDATE metas SET title=?, objetivo=?, program=?, indicadores=?, status=?, updated_at=NOW() 
                WHERE id=?
            ');
            $stmt->execute([
                $input['title'],
                $input['objetivo'] ?? '',
                $input['program'],
                json_encode($input['indicadores'] ?? []),
                $input['status'] ?? 'ativo',
                $id
            ]);
            echo json_encode(['success' => true]);
            break;
            
        case 'DELETE':
            $input = json_decode(file_get_contents('php://input'), true);
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
            $stmt = $pdo->prepare('
                INSERT INTO actions (titulo, programa, descricao, responsavel, status, created_by, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ');
            $stmt->execute([
                $input['titulo'],
                $input['programa'],
                $input['descricao'] ?? '',
                $input['responsavel'],
                $input['status'] ?? 'pending',
                $input['created_by'] ?? 'Unknown'
            ]);
            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
            break;
            
        case 'PUT':
            $input = json_decode(file_get_contents('php://input'), true);
            $id = $input['id'];
            $stmt = $pdo->prepare('
                UPDATE actions SET titulo=?, programa=?, descricao=?, responsavel=?, status=?, updated_at=NOW() 
                WHERE id=?
            ');
            $stmt->execute([
                $input['titulo'],
                $input['programa'],
                $input['descricao'] ?? '',
                $input['responsavel'],
                $input['status'] ?? 'pending',
                $id
            ]);
            echo json_encode(['success' => true]);
            break;
            
        case 'DELETE':
            $input = json_decode(file_get_contents('php://input'), true);
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
            echo json_encode(['success' => true, 'data' => $followups]);
            break;
            
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            $stmt = $pdo->prepare('
                INSERT INTO followups (target_id, type, mensagem, prioridade, prazo, colaboradores, attachments, status, created_by, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ');
            $stmt->execute([
                $input['target_id'],
                $input['type'],
                $input['mensagem'],
                $input['prioridade'] ?? 'media',
                $input['prazo'] ?? null,
                json_encode($input['colaboradores'] ?? []),
                json_encode($input['attachments'] ?? []),
                $input['status'] ?? 'pending',
                $input['created_by'] ?? 'Unknown'
            ]);
            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
            break;
            
        case 'PUT':
            $input = json_decode(file_get_contents('php://input'), true);
            $id = $input['id'];
            $stmt = $pdo->prepare('
                UPDATE followups SET target_id=?, type=?, mensagem=?, prioridade=?, prazo=?, colaboradores=?, attachments=?, status=? 
                WHERE id=?
            ');
            $stmt->execute([
                $input['target_id'],
                $input['type'],
                $input['mensagem'],
                $input['prioridade'] ?? 'media',
                $input['prazo'] ?? null,
                json_encode($input['colaboradores'] ?? []),
                json_encode($input['attachments'] ?? []),
                $input['status'] ?? 'pending',
                $id
            ]);
            echo json_encode(['success' => true]);
            break;
            
        case 'DELETE':
            $input = json_decode(file_get_contents('php://input'), true);
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
            echo json_encode(['success' => true, 'data' => $tasks]);
            break;
            
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            $stmt = $pdo->prepare('
                INSERT INTO tasks (followup_id, titulo, descricao, responsavel, status, prazo, attachments, created_by, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ');
            $stmt->execute([
                $input['followup_id'],
                $input['titulo'],
                $input['descricao'] ?? '',
                $input['responsavel'],
                $input['status'] ?? 'pending',
                $input['prazo'] ?? null,
                json_encode($input['attachments'] ?? []),
                $input['created_by'] ?? 'Unknown'
            ]);
            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
            break;
            
        case 'PUT':
            $input = json_decode(file_get_contents('php://input'), true);
            $id = $input['id'];
            $stmt = $pdo->prepare('
                UPDATE tasks SET followup_id=?, titulo=?, descricao=?, responsavel=?, status=?, prazo=?, attachments=? 
                WHERE id=?
            ');
            $stmt->execute([
                $input['followup_id'],
                $input['titulo'],
                $input['descricao'] ?? '',
                $input['responsavel'],
                $input['status'] ?? 'pending',
                $input['prazo'] ?? null,
                json_encode($input['attachments'] ?? []),
                $id
            ]);
            echo json_encode(['success' => true]);
            break;
            
        case 'DELETE':
            $input = json_decode(file_get_contents('php://input'), true);
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
            $stmt = $pdo->prepare('
                INSERT INTO cronograma (nome, inicio, fim, rubrica, executado, created_by, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ');
            $stmt->execute([
                $input['nome'],
                $input['inicio'] ?? null,
                $input['fim'] ?? null,
                $input['rubrica'] ?? 0,
                $input['executado'] ?? 0,
                $input['created_by'] ?? 'Unknown'
            ]);
            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
            break;
            
        case 'PUT':
            $input = json_decode(file_get_contents('php://input'), true);
            $id = $input['id'];
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
                $id
            ]);
            echo json_encode(['success' => true]);
            break;
            
        case 'DELETE':
            $input = json_decode(file_get_contents('php://input'), true);
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
            $stmt = $pdo->prepare('
                INSERT INTO inventario (programa, item, descricao, valor, atividades_relacionadas, created_by, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ');
            $stmt->execute([
                $input['programa'],
                $input['item'],
                $input['descricao'] ?? '',
                $input['valor'] ?? 0,
                $input['atividades_relacionadas'] ?? '',
                $input['created_by'] ?? 'Unknown'
            ]);
            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
            break;
            
        case 'PUT':
            $input = json_decode(file_get_contents('php://input'), true);
            $id = $input['id'];
            $stmt = $pdo->prepare('
                UPDATE inventario SET programa=?, item=?, descricao=?, valor=?, atividades_relacionadas=? 
                WHERE id=?
            ');
            $stmt->execute([
                $input['programa'],
                $input['item'],
                $input['descricao'] ?? '',
                $input['valor'] ?? 0,
                $input['atividades_relacionadas'] ?? '',
                $id
            ]);
            echo json_encode(['success' => true]);
            break;
            
        case 'DELETE':
            $input = json_decode(file_get_contents('php://input'), true);
            $stmt = $pdo->prepare('DELETE FROM inventario WHERE id = ?');
            $stmt->execute([$input['id']]);
            echo json_encode(['success' => true]);
            break;
    }
}
?>
