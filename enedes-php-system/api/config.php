<?php
$db_config = [
    'host'   => 'ep-mute-sound-aeprb25b-pooler.c-2.us-east-2.aws.neon.tech',
    'dbname' => 'neondb',
    'user'   => 'neondb_owner',
    'pass'   => 'npg_wX2ZKyd9tRbe'
];

try {
    $pdo = new PDO(
        "pgsql:host={$db_config['host']};dbname={$db_config['dbname']};sslmode=require",
        $db_config['user'],
        $db_config['pass'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("Erro na conexão com o banco: " . $e->getMessage());
}
?>
