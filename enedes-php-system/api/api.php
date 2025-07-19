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
$host = 'ep-gentle-unit-a5p9h5ux.us-east-2.aws.neon.tech';
$dbname = 'neondb';
$username = 'neondb_owner';
$password = 'SUA_SENHA_AQUI'; // <-- coloque sua senha real aqui

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
            $stmt = $pdo->prepare("INSERT INTO goals (title, objetivo, programa, indicadores, status, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->execute([
                $input['title'],
                $input['objetivo'] ?? '',
                $input['programa'] ?? '',
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
            if (!$input || !isset($input['programa']) || !isset($input['titulo'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Campos programa e titulo são obrigatórios']);
                break;
            }
            $stmt = $pdo->prepare("INSERT INTO actions (programa, titulo, descricao, responsavel, status, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->execute([
                $input['programa'],
                $input['titulo'],
                $input['descricao'] ?? '',
                $input['responsavel'] ?? '',
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
            if (!$input || !isset($input['action_id']) || !isset($input['comentario'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Campos action_id e comentario são obrigatórios']);
                break;
            }
            $stmt = $pdo->prepare("INSERT INTO followups (action_id, comentario, status, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([
                $input['action_id'],
                $input['comentario'],
                $input['status'] ?? 'pendente'
            ]);
            echo json_encode(['success' => true, 'message' => 'Follow-up criado com sucesso!']);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Método não permitido']);
        }
        break;

    // INVENTORY (inventário)
    case 'inventory':
        $pdo = conectarBanco();
        if ($method === 'GET') {
            $stmt = $pdo->query("SELECT * FROM inventory ORDER BY id DESC");
            echo json_encode($stmt->fetchAll());
        } elseif ($method === 'POST') {
            $input = getInput();
            if (!$input || !isset($input['programa']) || !isset($input['item'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Campos programa e item são obrigatórios']);
                break;
            }
            $stmt = $pdo->prepare("INSERT INTO inventory (programa, item, descricao, quantidade) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $input['programa'],
                $input['item'],
                $input['descricao'] ?? '',
                $input['quantidade'] ?? 0
            ]);
            echo json_encode(['success' => true, 'message' => 'Item de inventário criado com sucesso!']);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Método não permitido']);
        }
        break;

    // CRONOGRAMA
    case 'cronograma':
        $pdo = conectarBanco();
        if ($method === 'GET') {
            $stmt = $pdo->query("SELECT * FROM cronograma ORDER BY id DESC");
            echo json_encode($stmt->fetchAll());
        } elseif ($method === 'POST') {
            $input = getInput();
            if (!$input || !isset($input['meta_id']) || !isset($input['etapa'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Campos meta_id e etapa são obrigatórios']);
                break;
            }
            $stmt = $pdo->prepare("INSERT INTO cronograma (meta_id, etapa, inicio, prazo_final, rubrica, valor_executado) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $input['meta_id'],
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
