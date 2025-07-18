<?php
function listarActions($pdo) {
    $stmt = $pdo->query("SELECT * FROM actions");
    $actions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($actions);
}

function inserirAction($pdo) {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data) {
        http_response_code(400);
        echo json_encode(["error" => "Dados inválidos"]);
        return;
    }

    $stmt = $pdo->prepare("INSERT INTO actions (title, descricao, goal_id, status, created_at)
        VALUES (:title, :descricao, :goal_id, :status, NOW())");
    $stmt->execute([
        ':title' => $data['title'],
        ':descricao' => $data['descricao'],
        ':goal_id' => $data['goal_id'],
        ':status' => $data['status']
    ]);

    echo json_encode(["success" => true, "id" => $pdo->lastInsertId()]);
}

function atualizarAction($pdo) {
    parse_str(file_get_contents("php://input"), $data);

    if (!isset($data['id'])) {
        http_response_code(400);
        echo json_encode(["error" => "ID não informado"]);
        return;
    }

    $stmt = $pdo->prepare("UPDATE actions SET title = :title, descricao = :descricao, goal_id = :goal_id, status = :status WHERE id = :id");
    $stmt->execute([
        ':title' => $data['title'],
        ':descricao' => $data['descricao'],
        ':goal_id' => $data['goal_id'],
        ':status' => $data['status'],
        ':id' => $data['id']
    ]);

    echo json_encode(["success" => true]);
}

function deletarAction($pdo) {
    parse_str(file_get_contents("php://input"), $data);

    if (!isset($data['id'])) {
        http_response_code(400);
        echo json_encode(["error" => "ID não informado"]);
        return;
    }

    $stmt = $pdo->prepare("DELETE FROM actions WHERE id = :id");
    $stmt->execute([':id' => $data['id']]);

    echo json_encode(["success" => true]);
}
?>
