<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

function sendResponse($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data);
    exit();
}

// Configuração do banco de dados (Render PostgreSQL)
$host = 'dpg-d1u47ber433s73ebqecg-a.oregon-postgres.render.com';
$db   = 'enedesifb';
$user = 'enedesifb_user';
$pass = 'E8kQWf5R9eAUV6XZJBeYNVcgBdmcTjUB';
$port = '5432';

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$db", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    sendResponse(['error' => 'Erro na conexão com o banco: ' . $e->getMessage()], 500);
}

// Criar tabela metas se não existir
$pdo->exec("
    CREATE TABLE IF NOT EXISTS metas (
        id SERIAL PRIMARY KEY,
        title VARCHAR(200) NOT NULL,
        objetivo TEXT,
        program VARCHAR(100),
        indicadores JSON,
        created_by VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
");

$endpoint = $_GET['endpoint'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

switch ($endpoint) {
    case 'test':
        sendResponse([
            'status' => 'success',
            'message' => 'API funcionando!',
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
        break;

    case 'metas':
        if ($method === 'GET') {
            $stmt = $pdo->query("SELECT * FROM metas ORDER BY created_at DESC");
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            sendResponse(['success' => true, 'data' => $data]);
        } elseif ($method === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            if (empty($input['title'])) {
                sendResponse(['error' => 'Título é obrigatório'], 400);
            }
            $stmt = $pdo->prepare("INSERT INTO metas (title, objetivo, program, indicadores, created_by) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $input['title'],
                $input['objetivo'] ?? '',
                $input['program'] ?? '',
                isset($input['indicadores']) ? json_encode($input['indicadores']) : null,
                $input['created_by'] ?? 'Sistema',
            ]);
            $id = $pdo->lastInsertId();
            sendResponse(['success' => true, 'id' => $id, 'message' => 'Meta criada']);
        } elseif ($method === 'PUT') {
            $input = json_decode(file_get_contents('php://input'), true);
            if (empty($input['id'])) {
                sendResponse(['error' => 'ID é obrigatório'], 400);
            }
            $stmt = $pdo->prepare("UPDATE metas SET title = ?, objetivo = ?, program = ?, indicadores = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $stmt->execute([
                $input['title'] ?? '',
                $input['objetivo'] ?? '',
                $input['program'] ?? '',
                isset($input['indicadores']) ? json_encode($input['indicadores']) : null,
                $input['id']
            ]);
            sendResponse(['success' => true, 'message' => 'Meta atualizada']);
        } elseif ($method === 'DELETE') {
            $input = json_decode(file_get_contents('php://input'), true);
            if (empty($input['id'])) {
                sendResponse(['error' => 'ID é obrigatório'], 400);
            }
            $stmt = $pdo->prepare("DELETE FROM metas WHERE id = ?");
            $stmt->execute([$input['id']]);
            sendResponse(['success' => true, 'message' => 'Meta deletada']);
        } else {
            sendResponse(['error' => 'Método não permitido'], 405);
        }
        break;

    default:
        sendResponse(['error' => 'Endpoint não encontrado'], 404);
}
