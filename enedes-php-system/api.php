<?php
$host = 'dpg-d1u47ber433s73ebqecg-a.oregon-postgres.render.com'; // Host do banco
$db   = 'enedesifb';                                           // Banco correto conforme Render
$user = 'enedesifb_user';                                        // Usuário do banco
$pass = 'E8kQWf5R9eAUV6XZJBeYNVcgBdmcTjUB';                     // Senha correta
$port = '5432';                                                  // Porta padrão PostgreSQL
try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$db", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}
?>
