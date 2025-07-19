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

// Database configuration
class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $port;
    public $conn;

    public function __construct() {
        // Usando o banco vazio com senha conhecida
        $this->host = 'ep-mute-sound-aeprb25b-pooler.c-2.us-east-2.aws.neon.tech';
        $this->port = 5432;
        $this->db_name = 'neondb';
        $this->username = 'neondb_owner';
        $this->password = 'npg_wX2ZKyd9tRbe';
    }

    public function getConnection() {
        $this->conn = null;
        try {
            $dsn = "pgsql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name . ";sslmode=require";
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            sendResponse(['error' => 'Erro de conexão: ' . $exception->getMessage()], 500);
        }
        return $this->conn;
    }

    public function initializeTables() {
        $sql = "
        -- Goals table (principal para o frontend)
        CREATE TABLE IF NOT EXISTS goals (
            id SERIAL PRIMARY KEY,
            name VARCHAR(200) NOT NULL,
            description TEXT,
            category VARCHAR(100) DEFAULT 'geral',
            target_date DATE,
            status VARCHAR(50) DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        -- Actions table
        CREATE TABLE IF NOT EXISTS actions (
            id SERIAL PRIMARY KEY,
            goal_id INTEGER REFERENCES goals(id) ON DELETE CASCADE,
            description TEXT NOT NULL,
            status VARCHAR(50) DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        -- Users table
        CREATE TABLE IF NOT EXISTS users (
            id SERIAL PRIMARY KEY,
            username VARCHAR(80) UNIQUE NOT NULL,
            email VARCHAR(120) UNIQUE NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            role VARCHAR(50) NOT NULL,
            programa VARCHAR(100),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            is_active BOOLEAN DEFAULT TRUE
        );

        -- Metas table (sistema original)
        CREATE TABLE IF NOT EXISTS metas (
            id SERIAL PRIMARY KEY,
            titulo VARCHAR(200) NOT NULL,
            objetivo_social TEXT NOT NULL,
            programa VARCHAR(100),
            status VARCHAR(50) DEFAULT 'Ativa',
            created_by INTEGER REFERENCES users(id),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        -- Programas table
        CREATE TABLE IF NOT EXISTS programas (
            id SERIAL PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            description TEXT,
            icon VARCHAR(50) NOT NULL,
            color VARCHAR(20) NOT NULL,
            coordenador_id INTEGER REFERENCES users(id),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            is_active BOOLEAN DEFAULT TRUE
        );

        -- Acoes table
        CREATE TABLE IF NOT EXISTS acoes (
            id SERIAL PRIMARY KEY,
            titulo VARCHAR(200) NOT NULL,
            descricao TEXT NOT NULL,
            programa VARCHAR(100) NOT NULL,
            responsavel VARCHAR(100) NOT NULL,
            prazo DATE NOT NULL,
            status VARCHAR(50) DEFAULT 'Pendente',
            created_by INTEGER REFERENCES users(id),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        -- FollowUps table
        CREATE TABLE IF NOT EXISTS followups (
            id SERIAL PRIMARY KEY,
            tipo VARCHAR(20) NOT NULL,
            target_id INTEGER,
            programa VARCHAR(100),
            colaboradores JSON NOT NULL,
            mensagem TEXT NOT NULL,
            status VARCHAR(50) DEFAULT 'pending',
            created_by INTEGER REFERENCES users(id),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        -- Tasks table
        CREATE TABLE IF NOT EXISTS tasks (
            id SERIAL PRIMARY KEY,
            followup_id INTEGER REFERENCES followups(id),
            titulo VARCHAR(200) NOT NULL,
            descricao TEXT NOT NULL,
            responsavel VARCHAR(100) NOT NULL,
            prazo DATE NOT NULL,
            status VARCHAR(50) DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        -- Inventario table
        CREATE TABLE IF NOT EXISTS inventario (
            id SERIAL PRIMARY KEY,
            programa VARCHAR(100) NOT NULL,
            item VARCHAR(200) NOT NULL,
            quantidade INTEGER NOT NULL,
            status VARCHAR(50) DEFAULT 'Disponível',
            localizacao VARCHAR(200),
            observacoes TEXT,
            created_by INTEGER REFERENCES users(id),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        -- Etapas table
        CREATE TABLE IF NOT EXISTS etapas (
            id SERIAL PRIMARY KEY,
            programa VARCHAR(100) NOT NULL,
            titulo VARCHAR(200) NOT NULL,
            inicio DATE NOT NULL,
            prazo_final DATE NOT NULL,
            rubrica DECIMAL(10,2) DEFAULT 0.0,
            executado DECIMAL(10,2) DEFAULT 0.0,
            status_percentual INTEGER DEFAULT 0,
            created_by INTEGER REFERENCES users(id),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
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

// Session management
session_start();

// Route handling
switch ($endpoint) {
    case 'test':
        sendResponse([
            'status' => 'success',
            'message' => 'ENEDES API funcionando perfeitamente!',
            'timestamp' => date('Y-m-d H:i:s'),
            'method' => $method,
            'php_version' => phpversion(),
            'database_connected' => $db ? true : false,
            'neon_host' => 'ep-mute-sound-aeprb25b-pooler.c-2.us-east-2.aws.neon.tech',
            'database' => 'Fresh Database with Auto-Created Tables',
            'tables_created' => 'Automatically on first run'
        ]);
        break;

    case 'goals':
        if ($method === 'GET') {
            try {
                $stmt = $db->prepare("SELECT * FROM goals ORDER BY created_at DESC");
                $stmt->execute();
                $goals = $stmt->fetchAll();
                sendResponse($goals);
            } catch(PDOException $e) {
                sendResponse(['error' => 'Erro ao buscar goals: ' . $e->getMessage()], 500);
            }
        } elseif ($method === 'POST') {
            try {
                $input = json_decode(file_get_contents('php://input'), true);
                $name = $input['name'] ?? '';
                $description = $input['description'] ?? '';
                $category = $input['category'] ?? 'geral';
                $target_date = $input['target_date'] ?? null;

                if (!$name) {
                    sendResponse(['error' => 'Nome é obrigatório'], 400);
                }

                $stmt = $db->prepare("INSERT INTO goals (name, description, category, target_date) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $description, $category, $target_date]);

                $goal_id = $db->lastInsertId();
                $stmt = $db->prepare("SELECT * FROM goals WHERE id = ?");
                $stmt->execute([$goal_id]);
                $goal = $stmt->fetch();

                sendResponse($goal, 201);
            } catch(PDOException $e) {
                sendResponse(['error' => 'Erro ao criar goal: ' . $e->getMessage()], 500);
            }
        } elseif ($method === 'PUT') {
            try {
                $input = json_decode(file_get_contents('php://input'), true);
                $id = $_GET['id'] ?? $input['id'] ?? '';
                
                if (!$id) {
                    sendResponse(['error' => 'ID é obrigatório'], 400);
                }

                $stmt = $db->prepare("UPDATE goals SET name = ?, description = ?, category = ?, target_date = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
                $stmt->execute([
                    $input['name'] ?? '',
                    $input['description'] ?? '',
                    $input['category'] ?? 'geral',
                    $input['target_date'] ?? null,
                    $id
                ]);

                $stmt = $db->prepare("SELECT * FROM goals WHERE id = ?");
                $stmt->execute([$id]);
                $goal = $stmt->fetch();

                sendResponse($goal);
            } catch(PDOException $e) {
                sendResponse(['error' => 'Erro ao atualizar goal: ' . $e->getMessage()], 500);
            }
        } elseif ($method === 'DELETE') {
            try {
                $id = $_GET['id'] ?? '';
                
                if (!$id) {
                    sendResponse(['error' => 'ID é obrigatório'], 400);
                }

                $stmt = $db->prepare("DELETE FROM goals WHERE id = ?");
                $stmt->execute([$id]);

                sendResponse(['success' => true, 'message' => 'Goal deletado com sucesso']);
            } catch(PDOException $e) {
                sendResponse(['error' => 'Erro ao deletar goal: ' . $e->getMessage()], 500);
            }
        }
        break;

    case 'actions':
        if ($method === 'GET') {
            try {
                $goal_id = $_GET['goal_id'] ?? null;
                
                if ($goal_id) {
                    $stmt = $db->prepare("SELECT * FROM actions WHERE goal_id = ? ORDER BY created_at DESC");
                    $stmt->execute([$goal_id]);
                } else {
                    $stmt = $db->prepare("SELECT * FROM actions ORDER BY created_at DESC");
                    $stmt->execute();
                }
                
                $actions = $stmt->fetchAll();
                sendResponse($actions);
            } catch(PDOException $e) {
                sendResponse(['error' => 'Erro ao buscar actions: ' . $e->getMessage()], 500);
            }
        } elseif ($method === 'POST') {
            try {
                $input = json_decode(file_get_contents('php://input'), true);
                $goal_id = $input['goal_id'] ?? null;
                $description = $input['description'] ?? '';
                $status = $input['status'] ?? 'pending';

                if (!$goal_id || !$description) {
                    sendResponse(['error' => 'goal_id e description são obrigatórios'], 400);
                }

                $stmt = $db->prepare("INSERT INTO actions (goal_id, description, status) VALUES (?, ?, ?)");
                $stmt->execute([$goal_id, $description, $status]);

                $action_id = $db->lastInsertId();
                $stmt = $db->prepare("SELECT * FROM actions WHERE id = ?");
                $stmt->execute([$action_id]);
                $action = $stmt->fetch();

                sendResponse($action, 201);
            } catch(PDOException $e) {
                sendResponse(['error' => 'Erro ao criar action: ' . $e->getMessage()], 500);
            }
        } elseif ($method === 'PUT') {
            try {
                $input = json_decode(file_get_contents('php://input'), true);
                $id = $_GET['id'] ?? $input['id'] ?? '';
                
                if (!$id) {
                    sendResponse(['error' => 'ID é obrigatório'], 400);
                }

                $stmt = $db->prepare("UPDATE actions SET description = ?, status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
                $stmt->execute([
                    $input['description'] ?? '',
                    $input['status'] ?? 'pending',
                    $id
                ]);

                $stmt = $db->prepare("SELECT * FROM actions WHERE id = ?");
                $stmt->execute([$id]);
                $action = $stmt->fetch();

                sendResponse($action);
            } catch(PDOException $e) {
                sendResponse(['error' => 'Erro ao atualizar action: ' . $e->getMessage()], 500);
            }
        } elseif ($method === 'DELETE') {
            try {
                $id = $_GET['id'] ?? '';
                
                if (!$id) {
                    sendResponse(['error' => 'ID é obrigatório'], 400);
                }

                $stmt = $db->prepare("DELETE FROM actions WHERE id = ?");
                $stmt->execute([$id]);

                sendResponse(['success' => true, 'message' => 'Action deletada com sucesso']);
            } catch(PDOException $e) {
                sendResponse(['error' => 'Erro ao deletar action: ' . $e->getMessage()], 500);
            }
        }
        break;

    case 'cronograma':
        if ($method === 'POST') {
            try {
                $input = json_decode(file_get_contents('php://input'), true);
                sendResponse([
                    'success' => true,
                    'message' => 'Cronograma processado com sucesso',
                    'data' => $input
                ]);
            } catch (Exception $e) {
                sendResponse(['error' => 'Erro no cronograma: ' . $e->getMessage()], 500);
            }
        } else {
            sendResponse(['error' => 'Método não permitido'], 405);
        }
        break;

    default:
        sendResponse(['error' => 'Endpoint não encontrado: ' . $endpoint], 404);
}
?>
