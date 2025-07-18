<?php
$dsn = "pgsql:postgresql://neondb_owner:npg_wX27Kvd9tRbe@ep-mute-sound-aeprb25b-pooler.c-2.us-east-2.aws.neon.tech/neondb?sslmode=require&channel_binding=require";

try {
    $pdo = new PDO($dsn);
    // Configurações opcionais para o PDO
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    echo "Conexão bem sucedida!";
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erro na conexão com o banco de dados: " . $e->getMessage()]);
    exit;
}
?>
