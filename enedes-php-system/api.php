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

// Response helper (definir antes de usar)
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
        // Configuração correta para seu Neon.tech
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
            error_log("Connection error: " . $exception->getMessage());
            sendResponse(['error' => 'Erro de conexão: ' . $exception->getMessage()], 500);
        }
        return $this->conn;
    }

    public function initializeTables() {
        $sql = "
        -- Goals table (compatibilidade com frontend existente)
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

        -- Actions table (compatibilidade com frontend existente)
        CREATE TABLE IF NOT EXISTS actions (
            id SERIAL PRIMARY KEY,
            goal_id INTEGER REFERENCES goals(id),
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

        -- Metas table
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
            $this->initializeDefaultData();
        } catch(PDOException $exception) {
            error_log("Table creation error: " . $exception->getMessage());
        }
    }

    private function initializeDefaultData() {
        // Insert default users
        $default_users = [
            ['Coordenação Geral', 'coordenacao@enedes.com', '123456', 'coordenador_geral', null],
            ['IFB Mais Empreendedor', 'empreendedor@enedes.com', '123456', 'coordenador_programa', 'IFB Mais Empreendedor'],
            ['Rota Empreendedora', 'rota@enedes.com', '123456', 'coordenador_programa', 'Rota Empreendedora'],
            ['Lab Varejo', 'varejo@enedes.com', '123456', 'coordenador_programa', 'Lab Varejo'],
            ['Lab Consumer', 'consumer@enedes.com', '123456', 'coordenador_programa', 'Lab Consumer'],
            ['Estúdio', 'estudio@enedes.com', '123456', 'coordenador_programa', 'Estúdio'],
            ['IFB Digital', 'digital@enedes.com', '123456', 'coordenador_programa', 'IFB Digital'],
            ['Sala Interativa', 'sala@enedes.com', '123456', 'coordenador_programa', 'Sala Interativa'],
            ['Agência de Marketing', 'marketing@enedes.com', '123456', 'coordenador_programa', 'Agência de Marketing']
        ];

        foreach ($default_users as $user) {
            try {
                $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$user[1]]);
                if (!$stmt->fetch()) {
                    $stmt = $this->conn->prepare("INSERT INTO users (username, email, password_hash, role, programa) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$user[0], $user[1], password_hash($user[2], PASSWORD_DEFAULT), $user[3], $user[4]]);
                }
            } catch(Exception $e) {
                // Ignorar erros de dados padrão
            }
        }

        // Insert default programs
        $default_programs = [
            ['IFB Mais Empreendedor', 'Programa IFB Mais Empreendedor', 'lightbulb', 'blue'],
            ['Rota Empreendedora', 'Programa Rota Empreendedora', 'map', 'green'],
            ['Lab Varejo', 'Programa Lab Varejo', 'shopping-cart', 'purple'],
            ['Lab Consumer', 'Programa Lab Consumer', 'users', 'pink'],
            ['Estúdio', 'Programa Estúdio', 'camera', 'red'],
            ['IFB Digital', 'Programa IFB Digital', 'monitor', 'indigo'],
            ['Sala Interativa', 'Programa Sala Interativa', 'presentation', 'yellow'],
            ['Agência de Marketing', 'Programa Agência de Marketing', 'megaphone', 'orange']
        ];

        foreach ($default_programs as $program) {
            try {
                $stmt = $this->conn->prepare("SELECT id FROM programas WHERE name = ?");
                $stmt->execute([$program[0]]);
                if (!$stmt->fetch()) {
                    $stmt = $this->conn->prepare("INSERT INTO programas (name, description, icon, color) VALUES (?, ?, ?, ?)");
                    $stmt->execute($program);
                }
            } catch(Exception $e) {
                // Ignorar erros de dados padrão
            }
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

// Authentication helper
function requireAuth() {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Não autenticado']);
        exit();
    }
}

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
            'system' => 'ENEDES v2.0'
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

                // Deletar ações relacionadas primeiro
                $stmt = $db->prepare("DELETE FROM actions WHERE goal_id = ?");
                $stmt->execute([$id]);

                // Deletar goal
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

    case 'login':
        if ($method === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            $username = $input['username'] ?? '';
            $password = $input['password'] ?? '';

            if (!$username || !$password) {
                sendResponse(['error' => 'Username e password são obrigatórios'], 400);
            }

            $stmt = $db->prepare("SELECT * FROM users WHERE username = ? AND is_active = TRUE");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if (!$user || !password_verify($password, $user['password_hash'])) {
                sendResponse(['error' => 'Credenciais inválidas'], 401);
            }

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['programa'] = $user['programa'];

            sendResponse([
                'message' => 'Login realizado com sucesso',
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'role' => $user['role'],
                    'programa' => $user['programa']
                ]
            ]);
        }
        break;

    case 'logout':
        if ($method === 'POST') {
            session_destroy();
            sendResponse(['message' => 'Logout realizado com sucesso']);
        }
        break;

    case 'users':
        if ($method === 'GET') {
            $stmt = $db->prepare("SELECT id, username, role, programa FROM users WHERE is_active = TRUE ORDER BY username");
            $stmt->execute();
            $users = $stmt->fetchAll();
            sendResponse($users);
        }
        break;

    case 'metas':
        requireAuth();
        if ($method === 'GET') {
            $user_role = $_SESSION['role'];
            $user_programa = $_SESSION['programa'];

            if ($user_role === 'coordenador_geral') {
                $stmt = $db->prepare("SELECT * FROM metas ORDER BY created_at DESC");
                $stmt->execute();
            } else {
                $stmt = $db->prepare("SELECT * FROM metas WHERE programa = ? ORDER BY created_at DESC");
                $stmt->execute([$user_programa]);
            }

            $metas = $stmt->fetchAll();
            sendResponse($metas);
        } elseif ($method === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            $titulo = $input['titulo'] ?? '';
            $objetivo_social = $input['objetivo_social'] ?? '';
            $programa = $input['programa'] ?? '';

            if (!$titulo || !$objetivo_social) {
                sendResponse(['error' => 'Título e objetivo social são obrigatórios'], 400);
            }

            $stmt = $db->prepare("INSERT INTO metas (titulo, objetivo_social, programa, created_by) VALUES (?, ?, ?, ?)");
            $stmt->execute([$titulo, $objetivo_social, $programa, $_SESSION['user_id']]);

            $meta_id = $db->lastInsertId();
            $stmt = $db->prepare("SELECT * FROM metas WHERE id = ?");
            $stmt->execute([$meta_id]);
            $meta = $stmt->fetch();

            sendResponse($meta, 201);
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
