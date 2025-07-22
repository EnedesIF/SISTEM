<?php
function inserirAction($pdo) {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data) {
        http_response_code(400);
        echo json_encode(["error" => "Dados inválidos"]);
        return;
    }

    // Mapear campos do frontend
    $title = $data['titulo'] ?? $data['title'] ?? '';
    $descricao = $data['descricao'] ?? '';
    $programa = $data['programa'] ?? '';
    $responsavel = $data['responsavel'] ?? '';
    $status = $data['status'] ?? 'pending';

    if (empty($title)) {
        http_response_code(400);
        echo json_encode(["error" => "Título é obrigatório"]);
        return;
    }

    try {
        // CORRIGIDO: usar tabela "acoes" em vez de "actions"
        $stmt = $pdo->prepare("INSERT INTO acoes (title, descricao, programa, responsavel, status, created_at)
            VALUES (:title, :descricao, :programa, :responsavel, :status, NOW())");
        
        $stmt->execute([
            ':title' => $title,
            ':descricao' => $descricao,
            ':programa' => $programa,
            ':responsavel' => $responsavel,
            ':status' => $status
        ]);

        $id = $pdo->lastInsertId();
        echo json_encode([
            "success" => true, 
            "id" => $id,
            "message" => "Ação inserida com sucesso"
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Erro ao inserir ação: " . $e->getMessage()]);
    }
}

function listarActions($pdo) {
    try {
        // CORRIGIDO: usar tabela "acoes" em vez de "actions"
        $stmt = $pdo->query("SELECT * FROM acoes ORDER BY created_at DESC");
        $actions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["success" => true, "data" => $actions]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Erro ao listar ações: " . $e->getMessage()]);
    }
}

function atualizarAction($pdo) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['id'])) {
        http_response_code(400);
        echo json_encode(["error" => "ID não informado"]);
        return;
    }

    try {
        // CORRIGIDO: usar tabela "acoes" em vez de "actions"
        $stmt = $pdo->prepare("UPDATE acoes SET title = :title, descricao = :descricao, programa = :programa, responsavel = :responsavel, status = :status WHERE id = :id");
        $stmt->execute([
            ':title' => $data['titulo'] ?? $data['title'],
            ':descricao' => $data['descricao'],
            ':programa' => $data['programa'],
            ':responsavel' => $data['responsavel'],
            ':status' => $data['status'],
            ':id' => $data['id']
        ]);

        echo json_encode(["success" => true, "message" => "Ação atualizada"]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Erro ao atualizar ação: " . $e->getMessage()]);
    }
}

function deletarAction($pdo) {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id'])) {
        http_response_code(400);
        echo json_encode(["error" => "ID não informado"]);
        return;
    }

    try {
        // CORRIGIDO: usar tabela "acoes" em vez de "actions"
        $stmt = $pdo->prepare("DELETE FROM acoes WHERE id = :id");
        $stmt->execute([':id' => $data['id']]);

        echo json_encode(["success" => true, "message" => "Ação excluída"]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Erro ao excluir ação: " . $e->getMessage()]);
    }
}
?>

