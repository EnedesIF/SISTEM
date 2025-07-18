<?php
$host = 'ep-silent-rice-81223089.us-east-2.aws.neon.tech';
$db   = 'enedesdb';
$user = 'enedes_admin';
$pass = 'sua_senha_aqui'; // Substitua pela senha real
$charset = 'utf8mb4';

$dsn = "pgsql:host=$host;dbname=$db";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erro na conexão com o banco de dados: " . $e->getMessage()]);
    exit;
}
?>
