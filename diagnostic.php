<?php
// diagnostic.php - Diagnóstico EXCLUSIVO para Render PostgreSQL
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// ✅ CREDENCIAIS RENDER POSTGRESQL (da sua imagem)
$host = 'dpg-d1u47ber433s73ebqecg-a.oregon-postgres.render.com';
$port = '5432';
$dbname = 'enedesifb';
$username = 'enedesifb_user';
$password = 'E8kOWf5R9eAUV6XZJBeYNVcgBdmcTJUB';

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // ✅ Forçar autocommit para evitar problemas
    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, true);
    $pdo->exec("SET autocommit = ON");
    
    echo "✅ Conexão com Render PostgreSQL OK\n";
    echo "🌐 Host: $host\n";
    echo "💾 Database: $dbname\n";
    echo "👤 User: $username\n";
    
} catch (PDOException $e) {
    die("❌ Erro na conexão Render PostgreSQL: " . $e->getMessage());
}

// Teste de inserção com verificação
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['test'])) {
    
    try {
        // 1. Verificar se tabela metas existe
        $tableCheck = $pdo->query("SELECT to_regclass('public.metas')");
        $tableExists = $tableCheck->fetchColumn();
        
        if (!$tableExists) {
            echo "📋 Criando tabela 'metas'...\n";
            $pdo->exec("CREATE TABLE metas (
                id SERIAL PRIMARY KEY,
                title VARCHAR(255),
                titulo VARCHAR(255),
                objetivo TEXT,
                status VARCHAR(50) DEFAULT 'ativo',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
        }
        
        // 2. Contar registros antes
        $countBefore = $pdo->query("SELECT COUNT(*) FROM metas")->fetchColumn();
        echo "📊 Registros antes: $countBefore\n";
        
        // 3. Inserir com RETURNING
        $titulo = "Teste Render PostgreSQL - " . date('Y-m-d H:i:s');
        $sql = "INSERT INTO metas (title, titulo, objetivo, status, created_at) 
                VALUES (:title, :titulo, :objetivo, 'ativo', NOW()) 
                RETURNING id, title, created_at";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            'title' => $titulo,
            'titulo' => $titulo,
            'objetivo' => 'Teste de diagnóstico para Render PostgreSQL'
        ]);
        
        if ($result) {
            $inserted = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "✅ Inserido: ID {$inserted['id']}, Título: {$inserted['title']}\n";
            
            // 4. Contar depois
            $countAfter = $pdo->query("SELECT COUNT(*) FROM metas")->fetchColumn();
            echo "📊 Registros depois: $countAfter\n";
            
            // 5. Verificar se persiste
            $check = $pdo->prepare("SELECT * FROM metas WHERE id = :id");
            $check->execute(['id' => $inserted['id']]);
            $found = $check->fetch(PDO::FETCH_ASSOC);
            
            if ($found) {
                echo "🎉 SUCESSO! Registro persistiu no Render PostgreSQL!\n";
                echo "📋 Dados: " . json_encode($found, JSON_PRETTY_PRINT) . "\n";
                echo "🏆 Sistema 100% funcional com Render PostgreSQL!\n";
            } else {
                echo "❌ PROBLEMA: Registro não encontrado após inserção!\n";
            }
            
        } else {
            echo "❌ Falha na execução do INSERT\n";
            print_r($stmt->errorInfo());
        }
        
    } catch (Exception $e) {
        echo "❌ Erro no teste: " . $e->getMessage() . "\n";
        echo "Stack trace: " . $e->getTraceAsString() . "\n";
    }
}

// Endpoint para testar inserção via POST (compatível com frontend)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        echo json_encode(['error' => 'Dados inválidos']);
        exit;
    }
    
    try {
        // ✅ Usar transação explícita com commit forçado
        $pdo->beginTransaction();
        
        $sql = "INSERT INTO metas (title, titulo, objetivo, status, created_at) 
                VALUES (:title, :titulo, :objetivo, :status, NOW()) 
                RETURNING id";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            'title' => $input['title'] ?? $input['titulo'] ?? '',
            'titulo' => $input['titulo'] ?? $input['title'] ?? '',
            'objetivo' => $input['objetivo'] ?? '',
            'status' => $input['status'] ?? 'ativo'
        ]);
        
        if ($result) {
            $id = $stmt->fetchColumn();
            
            // ✅ COMMIT forçado
            $pdo->commit();
            
            echo json_encode([
                'success' => true,
                'message' => 'Meta inserida no Render PostgreSQL!',
                'id' => $id,
                'database_provider' => 'Render PostgreSQL',
                'fix_applied' => 'Transação commitada explicitamente'
            ]);
        } else {
            $pdo->rollback();
            echo json_encode(['error' => 'Falha na inserção', 'info' => $stmt->errorInfo()]);
        }
        
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollback();
        }
        echo json_encode(['error' => $e->getMessage()]);
    }
}

// Listar metas existentes
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['list'])) {
    try {
        $stmt = $pdo->query("SELECT * FROM metas ORDER BY created_at DESC LIMIT 10");
        $metas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'count' => count($metas),
            'data' => $metas,
            'database_provider' => 'Render PostgreSQL'
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}

// Instruções de uso
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['test']) && !isset($_GET['list'])) {
    echo json_encode([
        'message' => 'Diagnóstico Render PostgreSQL Ativo',
        'database_info' => [
            'provider' => 'Render PostgreSQL',
            'hostname' => $host,
            'database' => $dbname,
            'user' => $username,
            'status' => 'Conectado'
        ],
        'endpoints' => [
            'GET ?test=1' => 'Executar teste completo de inserção',
            'GET ?list=1' => 'Listar metas existentes',
            'POST (com JSON)' => 'Inserir nova meta'
        ],
        'credenciais_corretas' => [
            'host' => 'dpg-d1u47ber433s73ebqecg-a.oregon-postgres.render.com',
            'database' => 'enedesifb',
            'user' => 'enedesifb_user',
            'provider' => 'Render PostgreSQL'
        ],
        'fixes_aplicados' => [
            'render_postgresql_only' => true,
            'autocommit_forced' => true,
            'explicit_transactions' => true,
            'no_neon_references' => true
        ]
    ]);
}
?>
