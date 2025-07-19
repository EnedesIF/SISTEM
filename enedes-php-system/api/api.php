<?php
// ========================================
// ENEDES API - Backend PHP para Render
// ========================================

// Headers CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit('CORS OK');
}

// Configuração do banco Neon
$host = 'ep-mute-sound-aeprb25b-pooler.c-2.us-east-2.aws.neon.tech';
$dbname = 'neondb';
$username = 'neondb_owner';
$password = 'npg_wX2ZKyd9tRbe';

function conectarBanco() {
    global $host, $dbname, $username, $password;
    try {
        $dsn = "pgsql:host=$host;dbname=$dbname;sslmode=require";
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Erro de conexão: ' . $e->getMessage()]);
        exit();
    }
}

$method = $_SERVER['REQUEST_METHOD'];
$endpoint = $_GET['endpoint'] ?? '';

// Helper para leitura dos dados JSON enviados
function getInput() {
    return json_decode(file_get_contents('php://input'), true);
}

// ==================== ENDPOINTS ====================
switch ($endpoint) {
    case 'test':
        echo json_encode([
            'status' => 'success',
            'message' => 'ENEDES API funcionando!',
            'timestamp' => date('Y-m-d H:i:s'),
            'method' => $method,
            'php_version' => phpversion()
        ]);
        break;

    // GOALS (metas)
    case 'goals':
        $pdo = conectarBanco();
        if ($method === 'GET') {
            $stmt = $pdo->query("SELECT * FROM goals ORDER BY created_at DESC");
            echo json_encode($stmt->fetchAll());
        } elseif ($method === 'POST') {
            $input = getInput();
            if (!$input || !isset($input['title'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Campo title é obrigatório']);
                break;
            }
            $stmt = $pdo->prepare("INSERT INTO goals (title, objetivo, program, indicadores, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
            $stmt->execute([
                $input['title'],
                $input['objetivo'] ?? '',
                $input['program'] ?? '',
                isset($input['indicadores']) ? json_encode($input['indicadores']) : '[]',
                $input['status'] ?? 'ativo'
            ]);
            echo json_encode(['success' => true, 'message' => 'Meta criada com sucesso!']);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Método não permitido']);
        }
        break;

    // ACTIONS (ações)
    case 'actions':
        $pdo = conectarBanco();
        if ($method === 'GET') {
            $stmt = $pdo->query("SELECT * FROM actions ORDER BY created_at DESC");
            echo json_encode($stmt->fetchAll());
        } elseif ($method === 'POST') {
            $input = getInput();
            if (!$input || !isset($input['goal_id']) || !isset($input['description'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Campos goal_id e description são obrigatórios']);
                break;
            }
            $stmt = $pdo->prepare("INSERT INTO actions (goal_id, description, status, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
            $stmt->execute([
                $input['goal_id'],
                $input['description'],
                $input['status'] ?? 'pendente'
            ]);
            echo json_encode(['success' => true, 'message' => 'Ação criada com sucesso!']);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Método não permitido']);
        }
        break;

    // FOLLOWUPS (acompanhamentos)
    case 'followups':
        $pdo = conectarBanco();
        if ($method === 'GET') {
            $stmt = $pdo->query("SELECT * FROM followups ORDER BY created_at DESC");
            echo json_encode($stmt->fetchAll());
        } elseif ($method === 'POST') {
            $input = getInput();
            if (!$input || !isset($input['description'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Campo description é obrigatório']);
                break;
            }
            $stmt = $pdo->prepare("INSERT INTO followups (description, status, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
            $stmt->execute([
                $input['description'],
                $input['status'] ?? 'pendente'
            ]);
            echo json_encode(['success' => true, 'message' => 'Follow-up criado com sucesso!']);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Método não permitido']);
        }
        break;

    // TASKS (tarefas)
    case 'tasks':
        $pdo = conectarBanco();
        if ($method === 'GET') {
            $stmt = $pdo->query("SELECT * FROM tasks ORDER BY created_at DESC");
            echo json_encode($stmt->fetchAll());
        } elseif ($method === 'POST') {
            $input = getInput();
            if (!$input || !isset($input['description'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Campo description é obrigatório']);
                break;
            }
            $stmt = $pdo->prepare("INSERT INTO tasks (description, status, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
            $stmt->execute([
                $input['description'],
                $input['status'] ?? 'pendente'
            ]);
            echo json_encode(['success' => true, 'message' => 'Tarefa criada com sucesso!']);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Método não permitido']);
        }
        break;

    // INVENTORY (inventário)
    case 'inventory':
        $pdo = conectarBanco();
        if ($method === 'GET') {
            $stmt = $pdo->query("SELECT * FROM inventory ORDER BY created_at DESC");
            echo json_encode($stmt->fetchAll());
        } elseif ($method === 'POST') {
            $input = getInput();
            if (!$input || !isset($input['item'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Campo item é obrigatório']);
                break;
            }
            $stmt = $pdo->prepare("INSERT INTO inventory (item, quantity, status, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
            $stmt->execute([
                $input['item'],
                $input['quantity'] ?? 1,
                $input['status'] ?? 'ativo'
            ]);
            echo json_encode(['success' => true, 'message' => 'Item de inventário criado com sucesso!']);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Método não permitido']);
        }
        break;

    // SCHEDULE (cronograma)
    case 'schedule':
        $pdo = conectarBanco();
        if ($method === 'GET') {
            $stmt = $pdo->query("SELECT * FROM schedule ORDER BY created_at DESC");
            echo json_encode($stmt->fetchAll());
        } elseif ($method === 'POST') {
            $input = getInput();
            if (!$input || !isset($input['etapa'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Campo etapa é obrigatório']);
                break;
            }
            $stmt = $pdo->prepare("INSERT INTO schedule (etapa, inicio, prazo_final, rubrica, valor_executado, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
            $stmt->execute([
                $input['etapa'],
                $input['inicio'] ?? null,
                $input['prazo_final'] ?? null,
                $input['rubrica'] ?? '',
                $input['valor_executado'] ?? 0
            ]);
            echo json_encode(['success' => true, 'message' => 'Cronograma criado com sucesso!']);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Método não permitido']);
        }
        break;

    // Default: endpoint não encontrado
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint não encontrado: ' . $endpoint]);
}
?>
