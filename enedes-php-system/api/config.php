<?php
// config.php - Configuração EXCLUSIVA para Render PostgreSQL
// ✅ CREDENCIAIS RENDER POSTGRESQL (da sua imagem)

$host = 'dpg-d1u47ber433s73ebqecg-a.oregon-postgres.render.com';
$db   = 'enedesifb';
$user = 'enedesifb_user';
$pass = 'E8kOWf5R9eAUV6XZJBeYNVcgBdmcTJUB';
$port = '5432';

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$db", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_AUTOCOMMIT => true,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    // Garantir autocommit para Render PostgreSQL
    $pdo->exec("SET autocommit = ON");
    
    // Log de sucesso
    error_log("✅ Conectado ao Render PostgreSQL: $host/$db");
    
} catch (PDOException $e) {
    error_log("❌ Erro na conexão Render PostgreSQL: " . $e->getMessage());
    die("Erro na conexão com Render PostgreSQL: " . $e->getMessage());
}

// Informações de conexão
$connection_info = [
    'provider' => 'Render PostgreSQL',
    'hostname' => $host,
    'database' => $db,
    'username' => $user,
    'port' => $port,
    'status' => 'Connected',
    'timestamp' => date('Y-m-d H:i:s')
];

function getConnectionInfo() {
    global $connection_info;
    return $connection_info;
}
?>
