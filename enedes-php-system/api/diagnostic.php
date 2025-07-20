<?php
// DIAGNÓSTICO RÁPIDO - Substitua suas credenciais do Render aqui
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// SUAS CREDENCIAIS DO RENDER:
$host = 'dpg-d1u47ber433s73ebqecg-a.oregon-postgres.render.com';
$port = '5432';
$dbname = 'enedesifb';
$username = 'enedesifb_user';
$password = 'E8kQWf5R9eAUV6XZJBeYNVcgBdmcTjUB';

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 🔧 FIX PRINCIPAL: Forçar autocommit
    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, true);
    
    echo "✅ Conexão OK\n";
    
} catch (PDOException $e) {
    die("❌ Erro: " . $e->getMessage());
}

// Teste de inserção com verificação imediata
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['test'])) {
    
    try {
        // 1. Contar antes
        $countBefore = $pdo->query("SELECT COUNT(*) FROM metas")->fetchColumn();
        echo "📊 Registros antes: $countBefore\n";
        
        // 2. Inserir com RETURNING (garante que funcionou)
        $titulo = "Teste Fix - " . date('Y-m-d H:i:s');
        $sql = "INSERT INTO metas (titulo, descricao, status, created_at) 
                VALUES (:titulo, :descricao, 'ativa', NOW()) 
                RETURNING id, titulo, created_at";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            'titulo' => $titulo,
            'descricao' => 'Teste de diagnóstico rápido'
        ]);
        
        if ($result) {
            $inserted = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "✅ Inserido: ID {$inserted['id']}, Título: {$inserted['titulo']}\n";
            
            // 3. Contar depois
            $countAfter = $pdo->query("SELECT COUNT(*) FROM metas")->fetchColumn();
            echo "📊 Registros depois: $countAfter\n";
            
            // 4. Verificar se persiste (buscar o registro)
            $check = $pdo->prepare("SELECT * FROM metas WHERE id = :id");
            $check->execute(['id' => $inserted['id']]);
            $found = $check->fetch(PDO::FETCH_ASSOC);
            
            if ($found) {
                echo "🎉 SUCESSO! Registro persistiu no banco!\n";
                echo "📋 Dados: " . json_encode($found, JSON_PRETTY_PRINT) . "\n";
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

// Endpoint para sua API atual - com o FIX aplicado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        echo json_encode(['error' => 'Dados inválidos']);
        exit;
    }
    
    try {
        // 🔧 APLICAR O FIX: Usar transação explícita com commit forçado
        $pdo->beginTransaction();
        
        $sql = "INSERT INTO metas (titulo, descricao, prazo, status, created_at) 
                VALUES (:titulo, :descricao, :prazo, :status, NOW()) 
                RETURNING id";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            'titulo' => $input['titulo'] ?? '',
            'descricao' => $input['descricao'] ?? '',
            'prazo' => $input['prazo'] ?? null,
            'status' => $input['status'] ?? 'ativa'
        ]);
        
        if ($result) {
            $id = $stmt->fetchColumn();
            
            // 🔧 CRÍTICO: Commit forçado
            $pdo->commit();
            
            echo json_encode([
                'success' => true,
                'message' => 'Meta inserida com sucesso!',
                'id' => $id,
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
            'data' => $metas
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}

// Instruções de uso
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['test']) && !isset($_GET['list'])) {
    echo json_encode([
        'message' => 'API de Diagnóstico Ativa',
        'endpoints' => [
            'GET ?test=1' => 'Executar teste de inserção com diagnóstico',
            'GET ?list=1' => 'Listar metas existentes',
            'POST (com JSON)' => 'Inserir nova meta com fix aplicado'
        ],
        'fix_applied' => [
            'autocommit_forced' => true,
            'explicit_transactions' => true,
            'commit_verification' => true
        ]
    ]);
}
?>
