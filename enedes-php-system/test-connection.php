<?php
require_once('config.php');

try {
    $stmt = $pdo->query("SELECT NOW()");
    $row = $stmt->fetch();
    echo "Conexão OK! Hora do servidor: " . $row[0];
} catch (PDOException $e) {
    echo "Erro na conexão: " . $e->getMessage();
}
?>
