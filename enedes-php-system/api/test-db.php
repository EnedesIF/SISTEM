<?php
require 'config.php';

try {
    $stmt = $pdo->query('SELECT NOW()');
    $row = $stmt->fetch();
    echo "Conexão OK! Data e hora do servidor: " . $row['now'];
} catch (Exception $e) {
    echo "Erro ao executar consulta: " . $e->getMessage();
}
?>
