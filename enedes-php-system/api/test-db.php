<?php
$dsn = "pgsql:host=ep-mute-sound-aeprb25b-pooler.c-2.us-east-2.aws.neon.tech;port=5432;dbname=neondb;user=neondb_owner;password=npg_wX27Kvd9tRbe;sslmode=require;channel_binding=require";

try {
    $pdo = new PDO($dsn);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Conexão com o banco de dados realizada com sucesso!";

    // Testar uma consulta simples
    $stmt = $pdo->query("SELECT NOW()");
    $row = $stmt->fetch();
    echo "<br>Data e hora atual do banco: " . $row[0];

} catch (PDOException $e) {
    echo "Erro na conexão com o banco: " . $e->getMessage();
}
?>
