<?php
$host = 'ep-patient-dawn-aeikx4fl-pooler.c-2.us-east-2.aws.neon.tech';
$port = '5432';
$db   = 'neondb';
$user = 'neondb_owner';
$pass = 'npg_yjg2lwFJbZ1E';

$dsn = "pgsql:host=$host;port=$port;dbname=$db;sslmode=require";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    // echo "Conexão com o banco Neon realizada com sucesso!";
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erro na conexão com o banco de dados: " . $e->getMessage()]);
    exit;
}
?>
