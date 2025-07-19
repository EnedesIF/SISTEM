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
        $this->password = 'npg_wX2ZKyd9tRbe'; // ✅ SENHA CORRETA
    }

    public function getConnection() {
        $this->conn = null;
        try {
            $dsn = "pgsql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name . ";sslmode=require";
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            error_log("Connection error: " . $exception->getMessage());
            // Para debug, vamos mostrar o erro
            sendResponse(['error' => 'Erro de conexão com banco: ' . $exception->getMessage()], 500);
        }
        return $this->conn;
    }

    public function initializeTables() {
        $sql = "
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
            ['Coordenador de Projeto - Área 1', 'coord1@enedes.com', '123456', 'coordenador_programa', 'Área 1'],
            ['Coordenador de Projeto - Área 2', 'coord2@enedes.com', '123456', 'coordenador_programa', 'Área 2'],
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

// Response helper (definir antes de usar)
function sendResponse($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data);
    exit();
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
        // Endpoint de teste para diagnóstico
        sendResponse([
            'status' => 'success',
            'message' => 'ENEDES API funcionando perfeitamente!',
            'timestamp' => date('Y-m-d H:i:s'),
            'method' => $method,
            'php_version' => phpversion(),
            'database_connected' => $db ? true : false,
            'endpoint_requested' => $endpoint,
            'neon_host' => 'ep-mute-sound-aeprb25b-pooler.c-2.us-east-2.aws.neon.tech'
        ]);
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
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

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

    case 'me':
        if ($method === 'GET') {
            requireAuth();
            $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                sendResponse(['error' => 'Usuário não encontrado'], 404);
            }

            sendResponse([
                'id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role'],
                'programa' => $user['programa']
            ]);
        }
        break;

    case 'users':
        if ($method === 'GET') {
            $stmt = $db->prepare("SELECT id, username, role, programa FROM users WHERE is_active = TRUE ORDER BY username");
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            sendResponse($users);
        }
        break;

    case 'goals':
        // Compatibilidade com frontend existente
        if ($method === 'GET') {
            try {
                $stmt = $db->prepare("SELECT * FROM goals ORDER BY created_at DESC");
                $stmt->execute();
                $goals = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                $goal = $stmt->fetch(PDO::FETCH_ASSOC);

                sendResponse($goal, 201);
            } catch(PDOException $e) {
                sendResponse(['error' => 'Erro ao criar goal: ' . $e->getMessage()], 500);
            }
        }
        break;

    case 'actions':
        // Compatibilidade com frontend existente
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
                
                $actions = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                $action = $stmt->fetch(PDO::FETCH_ASSOC);

                sendResponse($action, 201);
            } catch(PDOException $e) {
                sendResponse(['error' => 'Erro ao criar action: ' . $e->getMessage()], 500);
            }
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

            $metas = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            $meta = $stmt->fetch(PDO::FETCH_ASSOC);

            sendResponse($meta, 201);
        }
        break;

    case 'programs':
    case 'programas':
        requireAuth();
        if ($method === 'GET') {
            $user_role = $_SESSION['role'];
            $user_programa = $_SESSION['programa'];

            if ($user_role === 'coordenador_geral') {
                $stmt = $db->prepare("SELECT * FROM programas WHERE is_active = TRUE ORDER BY name");
                $stmt->execute();
            } else {
                $stmt = $db->prepare("SELECT * FROM programas WHERE name = ? AND is_active = TRUE");
                $stmt->execute([$user_programa]);
            }

            $programas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            sendResponse($programas);
        }
        break;

    case 'dashboard':
        requireAuth();
        if ($method === 'GET') {
            $user_role = $_SESSION['role'];
            $user_programa = $_SESSION['programa'];

            // Get statistics
            if ($user_role === 'coordenador_geral') {
                $stmt = $db->prepare("SELECT COUNT(*) as total FROM metas");
                $stmt->execute();
                $total_metas = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

                $stmt = $db->prepare("SELECT COUNT(*) as total FROM acoes");
                $stmt->execute();
                $total_acoes = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

                $stmt = $db->prepare("SELECT COUNT(*) as total FROM followups");
                $stmt->execute();
                $total_followups = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            } else {
                $stmt = $db->prepare("SELECT COUNT(*) as total FROM metas WHERE programa = ?");
                $stmt->execute([$user_programa]);
                $total_metas = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

                $stmt = $db->prepare("SELECT COUNT(*) as total FROM acoes WHERE programa = ?");
                $stmt->execute([$user_programa]);
                $total_acoes = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

                $stmt = $db->prepare("SELECT COUNT(*) as total FROM followups WHERE programa = ?");
                $stmt->execute([$user_programa]);
                $total_followups = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            }

            sendResponse([
                'totals' => [
                    'metas' => $total_metas,
                    'acoes' => $total_acoes,
                    'followups' => $total_followups
                ]
            ]);
        }
        break;

    case 'acoes':
        requireAuth();
        if ($method === 'GET') {
            $programa = $_GET['programa'] ?? '';
            $user_role = $_SESSION['role'];
            $user_programa = $_SESSION['programa'];

            if ($user_role === 'coordenador_geral') {
                if ($programa) {
                    $stmt = $db->prepare("SELECT * FROM acoes WHERE programa = ? ORDER BY created_at DESC");
                    $stmt->execute([$programa]);
                } else {
                    $stmt = $db->prepare("SELECT * FROM acoes ORDER BY created_at DESC");
                    $stmt->execute();
                }
            } else {
                $stmt = $db->prepare("SELECT * FROM acoes WHERE programa = ? ORDER BY created_at DESC");
                $stmt->execute([$user_programa]);
            }

            $acoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            sendResponse($acoes);
        } elseif ($method === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            $titulo = $input['titulo'] ?? '';
            $descricao = $input['descricao'] ?? '';
            $programa = $input['programa'] ?? '';
            $responsavel = $input['responsavel'] ?? '';
            $prazo = $input['prazo'] ?? '';

            if (!$titulo || !$descricao || !$programa || !$responsavel || !$prazo) {
                sendResponse(['error' => 'Todos os campos são obrigatórios'], 400);
            }

            $stmt = $db->prepare("INSERT INTO acoes (titulo, descricao, programa, responsavel, prazo, created_by) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$titulo, $descricao, $programa, $responsavel, $prazo, $_SESSION['user_id']]);

            $acao_id = $db->lastInsertId();
            $stmt = $db->prepare("SELECT * FROM acoes WHERE id = ?");
            $stmt->execute([$acao_id]);
            $acao = $stmt->fetch(PDO::FETCH_ASSOC);

            sendResponse($acao, 201);
        }
        break;

    case 'followups':
        requireAuth();
        if ($method === 'GET') {
            $user_role = $_SESSION['role'];
            $user_programa = $_SESSION['programa'];

            if ($user_role === 'coordenador_geral') {
                $stmt = $db->prepare("SELECT * FROM followups ORDER BY created_at DESC");
                $stmt->execute();
            } else {
                $stmt = $db->prepare("SELECT * FROM followups WHERE programa = ? ORDER BY created_at DESC");
                $stmt->execute([$user_programa]);
            }

            $followups = $stmt->fetchAll(PDO::FETCH_ASSOC);
            sendResponse($followups);
        } elseif ($method === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            $tipo = $input['tipo'] ?? '';
            $target_id = $input['target_id'] ?? null;
            $programa = $input['programa'] ?? '';
            $colaboradores = $input['colaboradores'] ?? [];
            $mensagem = $input['mensagem'] ?? '';

            if (!$tipo || !$mensagem) {
                sendResponse(['error' => 'Tipo e mensagem são obrigatórios'], 400);
            }

            $stmt = $db->prepare("INSERT INTO followups (tipo, target_id, programa, colaboradores, mensagem, created_by) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$tipo, $target_id, $programa, json_encode($colaboradores), $mensagem, $_SESSION['user_id']]);

            $followup_id = $db->lastInsertId();
            $stmt = $db->prepare("SELECT * FROM followups WHERE id = ?");
            $stmt->execute([$followup_id]);
            $followup = $stmt->fetch(PDO::FETCH_ASSOC);

            sendResponse($followup, 201);
        }
        break;

    case 'tasks':
        requireAuth();
        if ($method === 'GET') {
            $followup_id = $_GET['followup_id'] ?? '';
            
            if ($followup_id) {
                $stmt = $db->prepare("SELECT * FROM tasks WHERE followup_id = ? ORDER BY created_at DESC");
                $stmt->execute([$followup_id]);
            } else {
                $stmt = $db->prepare("SELECT * FROM tasks ORDER BY created_at DESC");
                $stmt->execute();
            }

            $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
            sendResponse($tasks);
        } elseif ($method === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            $followup_id = $input['followup_id'] ?? '';
            $titulo = $input['titulo'] ?? '';
            $descricao = $input['descricao'] ?? '';
            $responsavel = $input['responsavel'] ?? '';
            $prazo = $input['prazo'] ?? '';

            if (!$followup_id || !$titulo || !$descricao || !$responsavel || !$prazo) {
                sendResponse(['error' => 'Todos os campos são obrigatórios'], 400);
            }

            $stmt = $db->prepare("INSERT INTO tasks (followup_id, titulo, descricao, responsavel, prazo) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$followup_id, $titulo, $descricao, $responsavel, $prazo]);

            $task_id = $db->lastInsertId();
            $stmt = $db->prepare("SELECT * FROM tasks WHERE id = ?");
            $stmt->execute([$task_id]);
            $task = $stmt->fetch(PDO::FETCH_ASSOC);

            sendResponse($task, 201);
        }
        break;

    case 'inventario':
        requireAuth();
        if ($method === 'GET') {
            $programa = $_GET['programa'] ?? '';
            $user_role = $_SESSION['role'];
            $user_programa = $_SESSION['programa'];

            if ($user_role === 'coordenador_geral') {
                if ($programa) {
                    $stmt = $db->prepare("SELECT * FROM inventario WHERE programa = ? ORDER BY created_at DESC");
                    $stmt->execute([$programa]);
                } else {
                    $stmt = $db->prepare("SELECT * FROM inventario ORDER BY created_at DESC");
                    $stmt->execute();
                }
            } else {
                $stmt = $db->prepare("SELECT * FROM inventario WHERE programa = ? ORDER BY created_at DESC");
                $stmt->execute([$user_programa]);
            }

            $inventario = $stmt->fetchAll(PDO::FETCH_ASSOC);
            sendResponse($inventario);
        } elseif ($method === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            $programa = $input['programa'] ?? '';
            $item = $input['item'] ?? '';
            $quantidade = $input['quantidade'] ?? 0;
            $status = $input['status'] ?? 'Disponível';
            $localizacao = $input['localizacao'] ?? '';
            $observacoes = $input['observacoes'] ?? '';

            if (!$programa || !$item || !$quantidade) {
                sendResponse(['error' => 'Programa, item e quantidade são obrigatórios'], 400);
            }

            $stmt = $db->prepare("INSERT INTO inventario (programa, item, quantidade, status, localizacao, observacoes, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$programa, $item, $quantidade, $status, $localizacao, $observacoes, $_SESSION['user_id']]);

            $inventario_id = $db->lastInsertId();
            $stmt = $db->prepare("SELECT * FROM inventario WHERE id = ?");
            $stmt->execute([$inventario_id]);
            $inventario_item = $stmt->fetch(PDO::FETCH_ASSOC);

            sendResponse($inventario_item, 201);
        }
        break;

    case 'etapas':
        requireAuth();
        if ($method === 'GET') {
            $user_role = $_SESSION['role'];
            $user_programa = $_SESSION['programa'];

            if ($user_role === 'coordenador_geral') {
                $stmt = $db->prepare("SELECT * FROM etapas ORDER BY inicio ASC");
                $stmt->execute();
            } else {
                $stmt = $db->prepare("SELECT * FROM etapas WHERE programa = ? ORDER BY inicio ASC");
                $stmt->execute([$user_programa]);
            }

            $etapas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            sendResponse($etapas);
        } elseif ($method === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            $programa = $input['programa'] ?? '';
            $titulo = $input['titulo'] ?? '';
            $inicio = $input['inicio'] ?? '';
            $prazo_final = $input['prazo_final'] ?? '';
            $rubrica = $input['rubrica'] ?? 0.0;

            if (!$programa || !$titulo || !$inicio || !$prazo_final) {
                sendResponse(['error' => 'Todos os campos são obrigatórios'], 400);
            }

            $stmt = $db->prepare("INSERT INTO etapas (programa, titulo, inicio, prazo_final, rubrica, created_by) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$programa, $titulo, $inicio, $prazo_final, $rubrica, $_SESSION['user_id']]);

            $etapa_id = $db->lastInsertId();
            $stmt = $db->prepare("SELECT * FROM etapas WHERE id = ?");
            $stmt->execute([$etapa_id]);
            $etapa = $stmt->fetch(PDO::FETCH_ASSOC);

            sendResponse($etapa, 201);
        }
        break;

    case 'delete_meta':
        requireAuth();
        if ($method === 'POST' || $method === 'DELETE') {
            $input = json_decode(file_get_contents('php://input'), true);
            $meta_id = $input['id'] ?? $_GET['id'] ?? '';

            if (!$meta_id) {
                sendResponse(['error' => 'ID da meta é obrigatório'], 400);
            }

            $stmt = $db->prepare("DELETE FROM metas WHERE id = ?");
            $result = $stmt->execute([$meta_id]);

            if ($result) {
                sendResponse(['message' => 'Meta excluída com sucesso']);
            } else {
                sendResponse(['error' => 'Erro ao excluir meta'], 500);
            }
        }
        break;

    case 'cronograma':
        // Endpoint para cronograma (compatibilidade)
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
