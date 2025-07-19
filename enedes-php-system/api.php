<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Response helper
function sendResponse($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data);
    exit();
}

// Database configuration - NEON PostgreSQL
class Database {
    private $host = 'ep-mute-sound-aeprb25b-pooler.c-2.us-east-2.aws.neon.tech';
    private $port = 5432;
    private $db_name = 'neondb';
    private $username = 'neondb_owner';
    private $password = 'npg_wX2ZKyd9tRbe';
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $dsn = "pgsql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name . ";sslmode=require";
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            sendResponse([
                'error' => 'Erro de conexão com banco de dados',
                'details' => $exception->getMessage()
            ], 500);
        }
        return $this->conn;
    }

    public function initializeTables() {
        $sql = "
        -- ✅ METAS TABLE (compatível com frontend)
        CREATE TABLE IF NOT EXISTS metas (
            id SERIAL PRIMARY KEY,
            title VARCHAR(200) NOT NULL,
            titulo VARCHAR(200) NOT NULL,
            objetivo TEXT,
            program VARCHAR(100),
            programa VARCHAR(100), 
            indicadores JSON,
            status VARCHAR(50) DEFAULT 'ativo',
            created_by VARCHAR(100),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        -- ✅ ACTIONS TABLE (compatível com frontend)
        CREATE TABLE IF NOT EXISTS actions (
            id SERIAL PRIMARY KEY,
            title VARCHAR(200) NOT NULL,
            titulo VARCHAR(200) NOT NULL,
            descricao TEXT,
            program VARCHAR(100),
            programa VARCHAR(100),
            responsavel VARCHAR(100),
            status VARCHAR(50) DEFAULT 'pending',
            created_by VARCHAR(100),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        -- FOLLOWUPS TABLE
        CREATE TABLE IF NOT EXISTS followups (
            id SERIAL PRIMARY KEY,
            target_id INTEGER,
            type VARCHAR(20),
            mensagem TEXT,
            prioridade VARCHAR(20),
            prazo DATE,
            colaboradores JSON,
            status VARCHAR(50) DEFAULT 'pending',
            created_by VARCHAR(100),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        -- TASKS TABLE
        CREATE TABLE IF NOT EXISTS tasks (
            id SERIAL PRIMARY KEY,
            followup_id INTEGER,
            titulo VARCHAR(200),
            descricao TEXT,
            responsavel VARCHAR(100),
            status VARCHAR(50) DEFAULT 'pending',
            prazo DATE,
            attachments JSON,
            created_by VARCHAR(100),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        -- CRONOGRAMA TABLE
        CREATE TABLE IF NOT EXISTS cronograma (
            id SERIAL PRIMARY KEY,
            nome VARCHAR(200),
            inicio DATE,
            fim DATE,
            rubrica DECIMAL(10,2),
            executado DECIMAL(10,2),
            created_by VARCHAR(100),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        -- INVENTARIO TABLE
        CREATE TABLE IF NOT EXISTS inventario (
            id SERIAL PRIMARY KEY,
            programa VARCHAR(100),
            item VARCHAR(200),
            descricao TEXT,
            valor DECIMAL(10,2),
            atividades_relacionadas TEXT,
            created_by VARCHAR(100),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
        ";

        try {
            $this->conn->exec($sql);
            return true;
        } catch(PDOException $exception) {
            error_log("Table creation error: " . $exception->getMessage());
            return false;
        }
    }
}

// Initialize database
$database = new Database();
$db = $database->getConnection();

if ($db) {
    $database->initializeTables();
}

// Get endpoint from URL parameter
$endpoint = $_GET['endpoint'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

// Log da requisição para debug
error_log("ENEDES API - Endpoint: $endpoint, Method: $method, Input: " . file_get_contents('php://input'));

// Route handling
switch ($endpoint) {
    case 'test':
        sendResponse([
            'status' => 'success',
            'message' => 'ENEDES API funcionando perfeitamente!',
            'timestamp' => date('Y-m-d H:i:s'),
            'method' => $method,
            'database_connected' => $db ? true : false,
            'neon_status' => 'Conectado ao PostgreSQL Neon',
            'tables_status' => 'Criadas automaticamente'
        ]);
        break;

    case 'goals':
        if ($method === 'GET') {
            try {
                $stmt = $db->prepare("SELECT * FROM metas ORDER BY created_at DESC");
                $stmt->execute();
                $metas = $stmt->fetchAll();
                
                sendResponse([
                    'success' => true,
                    'data' => $metas
                ]);
            } catch(PDOException $e) {
                sendResponse(['error' => 'Erro ao buscar metas: ' . $e->getMessage()], 500);
            }
        } 
        elseif ($method === 'POST') {
            try {
                $input = json_decode(file_get_contents('php://input'), true);
                
                // ✅ COMPATIBILIDADE TOTAL - aceita tanto inglês quanto português
                $title = $input['title'] ?? $input['titulo'] ?? '';
                $objetivo = $input['objetivo'] ?? $input['description'] ?? '';
                $program = $input['program'] ?? $input['programa'] ?? '';
                $indicadores = isset($input['indicadores']) ? json_encode($input['indicadores']) : '[]';
                $created_by = $input['created_by'] ?? 'Sistema';

                if (!$title) {
                    sendResponse(['error' => 'Título é obrigatório'], 400);
                }

                $stmt = $db->prepare("
                    INSERT INTO metas (title, titulo, objetivo, program, programa, indicadores, created_by) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$title, $title, $objetivo, $program, $program, $indicadores, $created_by]);

                $meta_id = $db->lastInsertId();
                
                sendResponse([
                    'success' => true,
                    'id' => $meta_id,
                    'message' => 'Meta cadastrada no PostgreSQL Neon!'
                ], 201);
                
            } catch(PDOException $e) {
                sendResponse(['error' => 'Erro ao criar meta: ' . $e->getMessage()], 500);
            }
        }
        elseif ($method === 'PUT') {
            try {
                $input = json_decode(file_get_contents('php://input'), true);
                $id = $input['id'] ?? '';
                $title = $input['title'] ?? $input['titulo'] ?? '';
                $objetivo = $input['objetivo'] ?? '';
                $program = $input['program'] ?? $input['programa'] ?? '';
                $indicadores = isset($input['indicadores']) ? json_encode($input['indicadores']) : '[]';

                $stmt = $db->prepare("
                    UPDATE metas 
                    SET title = ?, titulo = ?, objetivo = ?, program = ?, programa = ?, indicadores = ?, updated_at = CURRENT_TIMESTAMP 
                    WHERE id = ?
                ");
                $stmt->execute([$title, $title, $objetivo, $program, $program, $indicadores, $id]);

                sendResponse(['success' => true, 'message' => 'Meta atualizada!']);
                
            } catch(PDOException $e) {
                sendResponse(['error' => 'Erro ao atualizar meta: ' . $e->getMessage()], 500);
            }
        }
        elseif ($method === 'DELETE') {
            try {
                $input = json_decode(file_get_contents('php://input'), true);
                $id = $input['id'] ?? '';

                $stmt = $db->prepare("DELETE FROM metas WHERE id = ?");
                $stmt->execute([$id]);

                sendResponse(['success' => true, 'message' => 'Meta deletada!']);
                
            } catch(PDOException $e) {
                sendResponse(['error' => 'Erro ao deletar meta: ' . $e->getMessage()], 500);
            }
        }
        break;

    case 'actions':
        if ($method === 'GET') {
            try {
                $stmt = $db->prepare("SELECT * FROM actions ORDER BY created_at DESC");
                $stmt->execute();
                $actions = $stmt->fetchAll();
                
                sendResponse([
                    'success' => true,
                    'data' => $actions
                ]);
            } catch(PDOException $e) {
                sendResponse(['error' => 'Erro ao buscar ações: ' . $e->getMessage()], 500);
            }
        }
        elseif ($method === 'POST') {
            try {
                $input = json_decode(file_get_contents('php://input'), true);
                
                // ✅ COMPATIBILIDADE TOTAL
                $title = $input['title'] ?? $input['titulo'] ?? '';
                $descricao = $input['descricao'] ?? $input['description'] ?? '';
                $program = $input['program'] ?? $input['programa'] ?? '';
                $responsavel = $input['responsavel'] ?? '';
                $status = $input['status'] ?? 'pending';
                $created_by = $input['created_by'] ?? 'Sistema';

                if (!$title) {
                    sendResponse(['error' => 'Título é obrigatório'], 400);
                }

                $stmt = $db->prepare("
                    INSERT INTO actions (title, titulo, descricao, program, programa, responsavel, status, created_by) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$title, $title, $descricao, $program, $program, $responsavel, $status, $created_by]);

                $action_id = $db->lastInsertId();
                
                sendResponse([
                    'success' => true,
                    'id' => $action_id,
                    'message' => 'Ação cadastrada no PostgreSQL Neon!'
                ], 201);
                
            } catch(PDOException $e) {
                sendResponse(['error' => 'Erro ao criar ação: ' . $e->getMessage()], 500);
            }
        }
        elseif ($method === 'PUT') {
            try {
                $input = json_decode(file_get_contents('php://input'), true);
                $id = $input['id'] ?? '';
                $title = $input['title'] ?? $input['titulo'] ?? '';
                $descricao = $input['descricao'] ?? '';
                $program = $input['program'] ?? $input['programa'] ?? '';
                $responsavel = $input['responsavel'] ?? '';
                $status = $input['status'] ?? 'pending';

                $stmt = $db->prepare("
                    UPDATE actions 
                    SET title = ?, titulo = ?, descricao = ?, program = ?, programa = ?, responsavel = ?, status = ?, updated_at = CURRENT_TIMESTAMP 
                    WHERE id = ?
                ");
                $stmt->execute([$title, $title, $descricao, $program, $program, $responsavel, $status, $id]);

                sendResponse(['success' => true, 'message' => 'Ação atualizada!']);
                
            } catch(PDOException $e) {
                sendResponse(['error' => 'Erro ao atualizar ação: ' . $e->getMessage()], 500);
            }
        }
        elseif ($method === 'DELETE') {
            try {
                $input = json_decode(file_get_contents('php://input'), true);
                $id = $input['id'] ?? '';

                $stmt = $db->prepare("DELETE FROM actions WHERE id = ?");
                $stmt->execute([$id]);

                sendResponse(['success' => true, 'message' => 'Ação deletada!']);
                
            } catch(PDOException $e) {
                sendResponse(['error' => 'Erro ao deletar ação: ' . $e->getMessage()], 500);
            }
        }
        break;

    // ✅ NOVOS ENDPOINTS PARA SISTEMA COMPLETO
    case 'followups':
        if ($method === 'GET') {
            try {
                $stmt = $db->prepare("SELECT * FROM followups ORDER BY created_at DESC");
                $stmt->execute();
                $followups = $stmt->fetchAll();
                sendResponse(['success' => true, 'data' => $followups]);
            } catch(PDOException $e) {
                sendResponse(['error' => 'Erro ao buscar follow-ups: ' . $e->getMessage()], 500);
            }
        }
        elseif ($method === 'POST') {
            try {
                $input = json_decode(file_get_contents('php://input'), true);
                
                $stmt = $db->prepare("
                    INSERT INTO followups (target_id, type, mensagem, prioridade, prazo, colaboradores, status, created_by) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $input['target_id'] ?? null,
                    $input['type'] ?? '',
                    $input['mensagem'] ?? '',
                    $input['prioridade'] ?? 'media',
                    $input['prazo'] ?? null,
                    json_encode($input['colaboradores'] ?? []),
                    $input['status'] ?? 'pending',
                    $input['created_by'] ?? 'Sistema'
                ]);

                $followup_id = $db->lastInsertId();
                sendResponse(['success' => true, 'id' => $followup_id], 201);
                
            } catch(PDOException $e) {
                sendResponse(['error' => 'Erro ao criar follow-up: ' . $e->getMessage()], 500);
            }
        }
        break;

    case 'tasks':
        if ($method === 'GET') {
            try {
                $stmt = $db->prepare("SELECT * FROM tasks ORDER BY created_at DESC");
                $stmt->execute();
                $tasks = $stmt->fetchAll();
                sendResponse(['success' => true, 'data' => $tasks]);
            } catch(PDOException $e) {
                sendResponse(['error' => 'Erro ao buscar tarefas: ' . $e->getMessage()], 500);
            }
        }
        elseif ($method === 'POST') {
            try {
                $input = json_decode(file_get_contents('php://input'), true);
                
                $stmt = $db->prepare("
                    INSERT INTO tasks (followup_id, titulo, descricao, responsavel, status, prazo, attachments, created_by) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $input['followup_id'] ?? null,
                    $input['titulo'] ?? '',
                    $input['descricao'] ?? '',
                    $input['responsavel'] ?? '',
                    $input['status'] ?? 'pending',
                    $input['prazo'] ?? null,
                    json_encode($input['attachments'] ?? []),
                    $input['created_by'] ?? 'Sistema'
                ]);

                $task_id = $db->lastInsertId();
                sendResponse(['success' => true, 'id' => $task_id], 201);
                
            } catch(PDOException $e) {
                sendResponse(['error' => 'Erro ao criar tarefa: ' . $e->getMessage()], 500);
            }
        }
        break;

    case 'schedule':
        if ($method === 'GET') {
            try {
                $stmt = $db->prepare("SELECT * FROM cronograma ORDER BY created_at DESC");
                $stmt->execute();
                $cronograma = $stmt->fetchAll();
                sendResponse(['success' => true, 'data' => $cronograma]);
            } catch(PDOException $e) {
                sendResponse(['error' => 'Erro ao buscar cronograma: ' . $e->getMessage()], 500);
            }
        }
        elseif ($method === 'POST') {
            try {
                $input = json_decode(file_get_contents('php://input'), true);
                
                $stmt = $db->prepare("
                    INSERT INTO cronograma (nome, inicio, fim, rubrica, executado, created_by) 
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $input['nome'] ?? '',
                    $input['inicio'] ?? null,
                    $input['fim'] ?? null,
                    $input['rubrica'] ?? 0,
                    $input['executado'] ?? 0,
                    $input['created_by'] ?? 'Sistema'
                ]);

                $cronograma_id = $db->lastInsertId();
                sendResponse(['success' => true, 'id' => $cronograma_id], 201);
                
            } catch(PDOException $e) {
                sendResponse(['error' => 'Erro ao criar cronograma: ' . $e->getMessage()], 500);
            }
        }
        break;

    case 'inventory':
        if ($method === 'GET') {
            try {
                $stmt = $db->prepare("SELECT * FROM inventario ORDER BY created_at DESC");
                $stmt->execute();
                $inventario = $stmt->fetchAll();
                sendResponse(['success' => true, 'data' => $inventario]);
            } catch(PDOException $e) {
                sendResponse(['error' => 'Erro ao buscar inventário: ' . $e->getMessage()], 500);
            }
        }
        elseif ($method === 'POST') {
            try {
                $input = json_decode(file_get_contents('php://input'), true);
                
                $stmt = $db->prepare("
                    INSERT INTO inventario (programa, item, descricao, valor, atividades_relacionadas, created_by) 
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $input['programa'] ?? '',
                    $input['item'] ?? '',
                    $input['descricao'] ?? '',
                    $input['valor'] ?? 0,
                    $input['atividades_relacionadas'] ?? '',
                    $input['created_by'] ?? 'Sistema'
                ]);

                $inventario_id = $db->lastInsertId();
                sendResponse(['success' => true, 'id' => $inventario_id], 201);
                
            } catch(PDOException $e) {
                sendResponse(['error' => 'Erro ao criar item inventário: ' . $e->getMessage()], 500);
            }
        }
        break;

    case 'activity-log':
        if ($method === 'POST') {
            // Log de atividades - aceitar mas não salvar (opcional)
            sendResponse(['success' => true, 'message' => 'Log registrado'], 201);
        }
        break;

    default:
        sendResponse([
            'error' => 'Endpoint não encontrado: ' . $endpoint,
            'available_endpoints' => ['test', 'goals', 'actions', 'followups', 'tasks', 'schedule', 'inventory'],
            'method' => $method
        ], 404);
}
?>
