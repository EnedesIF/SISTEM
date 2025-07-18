<?php

// Listar todas as metas
function listarGoals($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM goals ORDER BY id ASC");
        $goals = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($goals);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
}

// Inserir nova meta
function inserirGoal($pdo) {
    $dados = json_decode(file_get_contents('php://input'), true);

    // Validação simples
    if (!$dados || !isset($dados['title']) || !isset($dados['objetivo'])) {
        http_response_code(400);
        echo json_encode(["error" => "Campos obrigatórios não enviados"]);
        return;
    }

    $sql = "INSERT INTO goals (title, objetivo, programa, indicadores, status, created_at) 
            VALUES (:title, :objetivo, :programa, :indicadores, :status, NOW()) RETURNING id";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':title' => $dados['title'],
            ':objetivo' => $dados['objetivo'],
            ':programa' => $dados['programa'] ?? null,
            ':indicadores' => isset($dados['indicadores']) ? json_encode($dados['indicadores']) : '[]',
            ':status' => $dados['status'] ?? 'ativo'
        ]);
        $id = $stmt->fetchColumn();
        echo json_encode(["success" => true, "id" => $id]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
}

// Atualizar meta existente
function atualizarGoal($pdo) {
    $dados = json_decode(file_get_contents('php://input'), true);

    if (!$dados || !isset($dados['id'])) {
        http_response_code(400);
        echo json_encode(["error" => "ID não informado"]);
        return;
    }

    $sql = "UPDATE goals SET 
        title = :title,
        objetivo = :objetivo,
        programa = :programa,
        indicadores = :indicadores,
        status = :status
        WHERE id = :id";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':title' => $dados['title'] ?? '',
            ':objetivo' => $dados['objetivo'] ?? '',
            ':programa' => $dados['programa'] ?? null,
            ':indicadores' => isset($dados['indicadores']) ? json_encode($dados['indicadores']) : '[]',
            ':status' => $dados['status'] ?? 'ativo',
            ':id' => $dados['id']
        ]);
        echo json_encode(["success" => true]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
}

// Deletar meta existente
function deletarGoal($pdo) {
    $dados = json_decode(file_get_contents('php://input'), true);

    if (!$dados || !isset($dados['id'])) {
        http_response_code(400);
        echo json_encode(["error" => "ID não informado"]);
        return;
    }

    $sql = "DELETE FROM goals WHERE id = :id";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $dados['id']]);
        echo json_encode(["success" => true]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
}

?>
