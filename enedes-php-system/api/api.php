<?php
require_once 'config.php';
require_once 'auth.php';
require_once 'cronograma.php';

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
    default:
        http_response_code(404);
        echo json_encode(["error" => "Endpoint não encontrado"]);
}
?>
