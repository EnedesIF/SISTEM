<?php
require_once 'config.php';
require_once 'auth.php';
require_once 'cronograma.php';
require_once 'goals.php';
require_once 'actions.php';

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
        if
