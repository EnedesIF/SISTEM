<?php
function inserirCronograma($pdo) {
    $dados = json_decode(file_get_contents('php://input'), true);
    if (!$dados) {
        http_response_code(400);
        echo json_encode(["error" => "Dados JSON inválidos"]);
        return;
    }

    $sql = "INSERT INTO cronograma (meta_id, etapa, inicio, prazo_final, rubrica, valor_executado)
            VALUES (:meta_id, :etapa, :inicio, :prazo_final, :rubrica, :valor_executado)";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':meta_id' => $dados['meta_id'] ?? null,
            ':etapa' => $dados['etapa'] ?? null,
            ':inicio' => $dados['inicio'] ?? null,
            ':prazo_final' => $dados['prazo_final'] ?? null,
            ':rubrica' => $dados['rubrica'] ?? null,
            ':valor_executado' => $dados['valor_executado'] ?? null
        ]);

        echo json_encode(["success" => true, "message" => "Dados inseridos com sucesso"]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Erro ao inserir dados: " . $e->getMessage()]);
    }
}
?>
