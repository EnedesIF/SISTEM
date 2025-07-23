<?php
// teste_tarefas.php - Teste de conexão com Render PostgreSQL
require_once('config.php');

try {
    // Teste de conexão básica
    $stmt = $pdo->query("SELECT NOW() as server_time, version() as pg_version");
    $row = $stmt->fetch();
    
    echo "<h2>✅ Conexão com Render PostgreSQL OK!</h2>";
    echo "<p><strong>Hora do servidor:</strong> " . $row['server_time'] . "</p>";
    echo "<p><strong>Versão PostgreSQL:</strong> " . $row['pg_version'] . "</p>";
    
    // Informações da conexão
    $connectionInfo = getConnectionInfo();
    echo "<h3>📊 Informações da Conexão:</h3>";
    echo "<ul>";
    echo "<li><strong>Provider:</strong> " . $connectionInfo['provider'] . "</li>";
    echo "<li><strong>Hostname:</strong> " . $connectionInfo['hostname'] . "</li>";
    echo "<li><strong>Database:</strong> " . $connectionInfo['database'] . "</li>";
    echo "<li><strong>Username:</strong> " . $connectionInfo['username'] . "</li>";
    echo "<li><strong>Status:</strong> " . $connectionInfo['status'] . "</li>";
    echo "</ul>";
    
    // Teste de criação de tabela
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS test_render (
            id SERIAL PRIMARY KEY,
            nome VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        echo "<p>✅ Tabela de teste criada com sucesso</p>";
        
        // Teste de inserção
        $stmt = $pdo->prepare("INSERT INTO test_render (nome) VALUES (:nome) RETURNING id");
        $stmt->execute(['nome' => 'Teste Render - ' . date('H:i:s')]);
        $id = $stmt->fetchColumn();
        
        echo "<p>✅ Registro inserido com ID: $id</p>";
        
        // Contar registros
        $count = $pdo->query("SELECT COUNT(*) FROM test_render")->fetchColumn();
        echo "<p>📊 Total de registros de teste: $count</p>";
        
    } catch (PDOException $e) {
        echo "<p>❌ Erro no teste de tabela: " . $e->getMessage() . "</p>";
    }
    
    echo "<h3>🎉 Render PostgreSQL funcionando perfeitamente!</h3>";
    echo "<p>Seu sistema está pronto para uso com o banco do Render.</p>";
    
} catch (PDOException $e) {
    echo "<h2>❌ Erro na conexão:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<h3>Verificações:</h3>";
    echo "<ul>";
    echo "<li>Credenciais do Render PostgreSQL estão corretas?</li>";
    echo "<li>Banco 'enedesifb' existe no Render?</li>";
    echo "<li>Usuário 'enedesifb_user' tem permissões?</li>";
    echo "</ul>";
}
?>
