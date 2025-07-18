<?php
require_once 'config.php';

if (isset($pdo) && $pdo instanceof PDO) {
    echo "✅ Conexão bem-sucedida com o banco Neon!";
} else {
    echo "❌ Erro na conexão!";
}
?>
