<?php
// actions.php - Configurado para Render PostgreSQL
require_once 'config.php';

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
        // ✅ USAR TABELA "actions" (conforme frontend espera)
        $stmt = $pdo->prepare("INSERT INTO actions (titulo, descricao, programa, responsavel, status, created_at)
            VALUES (:titulo, :descricao, :programa, :responsavel, :status, NOW()) RETURNING id");
        
        $stmt->execute([
            ':titulo' => $title,
            ':descricao' => $descricao,
            ':programa' => $programa,
            ':responsavel' => $responsavel,
            ':status' => $status
        ]);

        $id = $stmt->fetchColumn();
        echo json_encode([
            "success" => true, 
            "id" => $id,
            "message" => "Ação inserida no Render PostgreSQL com sucesso",
            "database_provider" => "Render PostgreSQL"
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Erro ao inserir ação: " . $e->getMessage()]);
    }
}

function listarActions($pdo) {
    try {
        // ✅ USAR TABELA "actions"
        $stmt = $pdo->query("SELECT * FROM actions ORDER BY created_at DESC");
        $actions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode([
            "success" => true, 
            "data" => $actions,
            "database_provider" => "Render PostgreSQL"
        ]);
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
        // ✅ USAR TABELA "actions"
        $stmt = $pdo->prepare("UPDATE actions SET titulo = :titulo, descricao = :descricao, programa = :programa, responsavel = :responsavel, status = :status, updated_at = NOW() WHERE id = :id");
        $stmt->execute([
            ':titulo' => $data['titulo'] ?? $data['title'],
            ':descricao' => $data['descricao'],
            ':programa' => $data['programa'],
            ':responsavel' => $data['responsavel'],
            ':status' => $data['status'],
            ':id' => $data['id']
        ]);

        echo json_encode([
            "success" => true, 
            "message" => "Ação atualizada no Render PostgreSQL",
            "database_provider" => "Render PostgreSQL"
        ]);
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
        // ✅ USAR TABELA "actions"
        $stmt = $pdo->prepare("DELETE FROM actions WHERE id = :id");
        $stmt->execute([':id' => $data['id']]);

        echo json_encode([
            "success" => true, 
            "message" => "Ação excluída do Render PostgreSQL",
            "database_provider" => "Render PostgreSQL"
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Erro ao excluir ação: " . $e->getMessage()]);
    }
}
?>
