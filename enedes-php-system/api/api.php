<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/cronograma.php';
require_once __DIR__ . '/goals.php';
require_once __DIR__ . '/actions.php';

$method = $_SERVER['REQUEST_METHOD'];
$endpoint = $_GET['endpoint'] ?? '';

header('Content-Type: application/json');

switch ($endpoint) {
    case 'cronograma':
        if ($method === 'POST') {
            inserirCronograma($pdo);
        } else {
            http_response_code(405);
            echo json_encode(["error" => "Método não permitido"]);
        }
        break;
    case 'goals':
        if ($method === 'GET') {
            listarGoals($pdo);
        } elseif ($method === 'POST') {
            inserirGoal($pdo);
        } elseif ($method === 'PUT') {
            atualizarGoal($pdo);
        } elseif ($method === 'DELETE') {
            deletarGoal($pdo);
        } else {
            http_response_code(405);
            echo json_encode(["error" => "Método não permitido"]);
        }
        break;
    case 'actions':
        if ($method === 'GET') {
            listarActions($pdo);
        } elseif ($method === 'POST') {
            inserirAction($pdo);
        } elseif ($method === 'PUT') {
            atualizarAction($pdo);
        } elseif ($method === 'DELETE') {
            deletarAction($pdo);
        } else {
            http_response_code(405);
            echo json_encode(["error" => "Método não permitido"]);
        }
        break;
    default:
        http_response_code(404);
        echo json_encode(["error" => "Endpoint não encontrado"]);
}
?>
