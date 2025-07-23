<?php
// goals.php - Configurado para Render PostgreSQL
require_once 'config.php';

// Listar todas as metas
function listarGoals($pdo) {
    try {
        // ✅ USAR TABELA "metas" no Render PostgreSQL
        $stmt = $pdo->query("SELECT * FROM metas ORDER BY id ASC");
        $goals = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Garantir compatibilidade com frontend
        $goalsFormatted = array_map(function($meta) {
            // Garantir que tanto 'title' quanto 'titulo' existam
            if (!$meta['titulo'] && $meta['title']) {
                $meta['titulo'] = $meta['title'];
            }
            if (!$meta['title'] && $meta['titulo']) {
                $meta['title'] = $meta['titulo'];
            }
            
            // Garantir que tanto 'program' quanto 'programa' existam
            if (!$meta['programa'] && $meta['program']) {
                $meta['programa'] = $meta['program'];
            }
            if (!$meta['program'] && $meta['programa']) {
                $meta['program'] = $meta['programa'];
            }
            
            // Decodificar JSON de indicadores
            if (isset($meta['indicadores']) && is_string($meta['indicadores'])) {
                $meta['indicadores'] = json_decode($meta['indicadores'], true) ?: [];
            }
            
            return $meta;
        }, $goals);
        
        echo json_encode([
            "success" => true,
            "data" => $goalsFormatted,
            "database_provider" => "Render PostgreSQL"
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
}

// Inserir nova meta
function inserirGoal($pdo) {
    $dados = json_decode(file_get_contents('php://input'), true);

    // Validação
    if (!$dados || (!isset($dados['title']) && !isset($dados['titulo']))) {
        http_response_code(400);
        echo json_encode(["error" => "Campo title/titulo é obrigatório"]);
        return;
    }

    // Mapear campos para compatibilidade
    $title = $dados['title'] ?? $dados['titulo'] ?? '';
    $objetivo = $dados['objetivo'] ?? '';
    $program = $dados['program'] ?? $dados['programa'] ?? '';
    $indicadores = $dados['indicadores'] ?? [];
    $status = $dados['status'] ?? 'ativo';
    $created_by = $dados['created_by'] ?? 'Sistema';

    try {
        // ✅ USAR TABELA "metas" com campos híbridos
        $sql = "INSERT INTO metas (title, titulo, objetivo, program, programa, indicadores, status, created_by, created_at) 
                VALUES (:title, :titulo, :objetivo, :program, :programa, :indicadores, :status, :created_by, NOW()) RETURNING id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':title' => $title,
            ':titulo' => $title,
            ':objetivo' => $objetivo,
            ':program' => $program,
            ':programa' => $program,
            ':indicadores' => json_encode($indicadores),
            ':status' => $status,
            ':created_by' => $created_by
        ]);
        
        $id = $stmt->fetchColumn();
        echo json_encode([
            "success" => true, 
            "id" => $id,
            "message" => "Meta inserida no Render PostgreSQL com sucesso",
            "database_provider" => "Render PostgreSQL"
        ]);
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

    $title = $dados['title'] ?? $dados['titulo'] ?? '';
    $objetivo = $dados['objetivo'] ?? '';
    $program = $dados['program'] ?? $dados['programa'] ?? '';
    $indicadores = $dados['indicadores'] ?? [];
    $status = $dados['status'] ?? 'ativo';

    try {
        // ✅ USAR TABELA "metas"
        $sql = "UPDATE metas SET 
            title = :title,
            titulo = :titulo,
            objetivo = :objetivo,
            program = :program,
            programa = :programa,
            indicadores = :indicadores,
            status = :status,
            updated_at = NOW()
            WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':title' => $title,
            ':titulo' => $title,
            ':objetivo' => $objetivo,
            ':program' => $program,
            ':programa' => $program,
            ':indicadores' => json_encode($indicadores),
            ':status' => $status,
            ':id' => $dados['id']
        ]);
        
        echo json_encode([
            "success" => true,
            "message" => "Meta atualizada no Render PostgreSQL",
            "database_provider" => "Render PostgreSQL"
        ]);
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

    try {
        // ✅ USAR TABELA "metas"
        $sql = "DELETE FROM metas WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $dados['id']]);
        
        echo json_encode([
            "success" => true,
            "message" => "Meta excluída do Render PostgreSQL",
            "database_provider" => "Render PostgreSQL"
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
}
?>
