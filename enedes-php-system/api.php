<?php
// Headers CORS para permitir requisições do frontend
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Tratar requisições OPTIONS (preflight CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Configuração do banco PostgreSQL
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
    http_response_code(500);
    echo json_encode(["error" => "Erro na conexão: " . $e->getMessage()]);
    exit();
}

// Incluir arquivos com funções (ajustar caminhos conforme estrutura)
require_once('api/actions.php');
require_once('api/goals.php');
require_once('api/cronograma.php');

// Obter endpoint da URL
$endpoint = $_GET['endpoint'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

// Roteamento das requisições
try {
    switch ($endpoint) {
        case 'test':
            echo json_encode(['status' => 'ok', 'message' => 'API funcionando', 'time' => date('Y-m-d H:i:s')]);
            break;
            
        case 'goals':
            switch ($method) {
                case 'GET':
                    listarGoals($pdo);
                    break;
                case 'POST':
                    inserirGoal($pdo);
                    break;
                case 'PUT':
                    atualizarGoal($pdo);
                    break;
                case 'DELETE':
                    deletarGoal($pdo);
                    break;
                default:
                    http_response_code(405);
                    echo json_encode(['error' => 'Método não permitido para goals']);
            }
            break;
            
        case 'actions':
            switch ($method) {
                case 'GET':
                    listarActions($pdo);
                    break;
                case 'POST':
                    inserirAction($pdo);
                    break;
                case 'PUT':
                    atualizarAction($pdo);
                    break;
                case 'DELETE':
                    deletarAction($pdo);
                    break;
                default:
                    http_response_code(405);
                    echo json_encode(['error' => 'Método não permitido para actions']);
            }
            break;
            
        case 'cronograma':
            switch ($method) {
                case 'GET':
                    listarCronograma($pdo);
                    break;
                case 'POST':
                    inserirCronograma($pdo);
                    break;
                default:
                    http_response_code(405);
                    echo json_encode(['error' => 'Método não permitido para cronograma']);
            }
            break;
            
        default:
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint não encontrado: ' . $endpoint]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro interno do servidor: ' . $e->getMessage()]);
}
?>

