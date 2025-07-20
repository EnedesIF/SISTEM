<?php
$host = 'dpg-d1u47ber433s73ebqecg-a';         // Host
$db   = 'enedesifb';                          // Database name
$user = 'enedesifb_user';                     // Usuário
$pass = 'E8kQWf5R9eAUV6XZJBeYNVcgBdmcTjUB';   // Senha
$port = '5432';                               // Porta padrão

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$db", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}
?>
