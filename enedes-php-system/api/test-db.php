<?php
$host = 'ep-mute-sound-aeprb25b-pooler.c-2.us-east-2.aws.neon.tech';
$port = 5432;
$db   = 'neondb';
$user = 'neondb_owner';
$pass = 'npg_wX2ZKyd9tRbe'; // senha correta
$sslmode = 'require';

$dsn = "pgsql:host=$host;port=$port;dbname=$db;sslmode=$sslmode";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "Conexão com banco OK!";
} catch (PDOException $e) {
    http_response_code(500);
    echo "Erro na conexão com o banco: " . $e->getMessage();
    exit;
}
?>
