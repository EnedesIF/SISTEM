<?php
// cronograma.php - Configurado para Render PostgreSQL
require_once 'config.php';

function inserirCronograma($pdo) {
    $dados = json_decode(file_get_contents('php://input'), true);
    if (!$dados) {
        http_response_code(400);
        echo json_encode(["error" => "Dados JSON inválidos"]);
        return;
    }

    try {
        // ✅ USAR TABELA "cronograma" no Render PostgreSQL
        $sql = "INSERT INTO cronograma (nome, inicio, fim, rubrica, executado, created_by, created_at)
                VALUES (:nome, :inicio, :fim, :rubrica, :executado, :created_by, NOW()) RETURNING id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nome' => $dados['nome'] ?? $dados['etapa'] ?? '',
            ':inicio' => $dados['inicio'] ?? null,
            ':fim' => $dados['fim'] ?? $dados['prazo_final'] ?? null,
            ':rubrica' => $dados['rubrica'] ?? 0,
            ':executado' => $dados['executado'] ?? $dados['valor_executado'] ?? 0,
            ':created_by' => $dados['created_by'] ?? 'Sistema'
        ]);

        $id = $stmt->fetchColumn();
        echo json_encode([
            "success" => true,
            "id" => $id,
            "message" => "Etapa do cronograma inserida no Render PostgreSQL",
            "database_provider" => "Render PostgreSQL"
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Erro ao inserir dados: " . $e->getMessage()]);
    }
}

function listarCronograma($pdo) {
    try {
        // ✅ USAR TABELA "cronograma"
        $stmt = $pdo->query("SELECT * FROM cronograma ORDER BY created_at DESC");
        $cronograma = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            "success" => true,
            "data" => $cronograma,
            "database_provider" => "Render PostgreSQL"
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Erro ao listar cronograma: " . $e->getMessage()]);
    }
}

function atualizarCronograma($pdo) {
    $dados = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($dados['id'])) {
        http_response_code(400);
        echo json_encode(["error" => "ID não informado"]);
        return;
    }

    try {
        // ✅ USAR TABELA "cronograma"
        $sql = "UPDATE cronograma SET 
                nome = :nome, 
                inicio = :inicio, 
                fim = :fim, 
                rubrica = :rubrica, 
                executado = :executado,
                updated_at = NOW()
                WHERE id = :id";
                
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nome' => $dados['nome'] ?? '',
            ':inicio' => $dados['inicio'] ?? null,
            ':fim' => $dados['fim'] ?? null,
            ':rubrica' => $dados['rubrica'] ?? 0,
            ':executado' => $dados['executado'] ?? 0,
            ':id' => $dados['id']
        ]);

        echo json_encode([
            "success" => true,
            "message" => "Etapa do cronograma atualizada no Render PostgreSQL",
            "database_provider" => "Render PostgreSQL"
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Erro ao atualizar cronograma: " . $e->getMessage()]);
    }
}

function deletarCronograma($pdo) {
    $dados = json_decode(file_get_contents('php://input'), true);

    if (!isset($dados['id'])) {
        http_response_code(400);
        echo json_encode(["error" => "ID não informado"]);
        return;
    }

    try {
        // ✅ USAR TABELA "cronograma"
        $stmt = $pdo->prepare("DELETE FROM cronograma WHERE id = :id");
        $stmt->execute([':id' => $dados['id']]);

        echo json_encode([
            "success" => true,
            "message" => "Etapa do cronograma excluída do Render PostgreSQL",
            "database_provider" => "Render PostgreSQL"
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Erro ao excluir etapa: " . $e->getMessage()]);
    }
}
?>
