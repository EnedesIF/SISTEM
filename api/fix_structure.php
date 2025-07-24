<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Função de conexão igual ao api.php
function getDbConnection() {
    $database_url = getenv('DATABASE_URL');
    
    if (!$database_url) {
        $host = 'dpg-d1u47ber433s73ebqecg-a.oregon-postgres.render.com';
        $dbname = 'enedesifb';
        $username = 'enedesifb_user';
        $password = 'E8kOWf5R9eAUV6XZJBeYNVcgBdmcTJUB';
        $port = 5432;
        $database_url = "postgresql://$username:$password@$host:$port/$dbname";
    }
    
    $db = parse_url($database_url);
    $host = $db['host'] ?? 'localhost';
    $port = isset($db['port']) ? (int)$db['port'] : 5432;
    $dbname = isset($db['path']) ? ltrim($db['path'], '/') : 'enedesifb';
    $username = $db['user'] ?? '';
    $password = $db['pass'] ?? '';
    
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";
    
    return new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_TIMEOUT => 30
    ]);
}

function analyzeCurrentStructure($pdo) {
    $results = [];
    
    // Verificar tabelas existentes
    $stmt = $pdo->query("
        SELECT table_name 
        FROM information_schema.tables 
        WHERE table_schema = 'public'
        ORDER BY table_name
    ");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $results['existing_tables'] = $tables;
    
    // Analisar estrutura de cada tabela
    foreach ($tables as $table) {
        $stmt = $pdo->query("
            SELECT column_name, data_type, is_nullable, column_default
            FROM information_schema.columns 
            WHERE table_name = '$table' AND table_schema = 'public'
            ORDER BY ordinal_position
        ");
        $columns = $stmt->fetchAll();
        $results['table_structures'][$table] = $columns;
    }
    
    return $results;
}

function fixDatabaseStructure($pdo) {
    $results = [];
    $actions = [];
    
    try {
        // 1. Verificar e corrigir tabela metas
        $stmt = $pdo->query("
            SELECT column_name 
            FROM information_schema.columns 
            WHERE table_name = 'metas' AND table_schema = 'public'
        ");
        $metas_columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (in_array('title', $metas_columns) && !in_array('titulo', $metas_columns)) {
            // Adicionar coluna titulo como alias/cópia de title
            $pdo->exec("ALTER TABLE metas ADD COLUMN IF NOT EXISTS titulo VARCHAR(255)");
            $pdo->exec("UPDATE metas SET titulo = title WHERE titulo IS NULL");
            $actions[] = "Adicionada coluna 'titulo' na tabela metas";
        }
        
        // 2. Verificar e corrigir tabela actions
        $stmt = $pdo->query("
            SELECT column_name 
            FROM information_schema.columns 
            WHERE table_name = 'actions' AND table_schema = 'public'
        ");
        $actions_columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (!in_array('titulo', $actions_columns)) {
            $pdo->exec("ALTER TABLE actions ADD COLUMN IF NOT EXISTS titulo VARCHAR(255)");
            $actions[] = "Adicionada coluna 'titulo' na tabela actions (se não existia)";
        }
        
        // 3. Criar tabelas compatíveis se não existirem
        $tables_to_create = [
            'metas_unified' => "
                CREATE TABLE IF NOT EXISTS metas_unified (
                    id SERIAL PRIMARY KEY,
                    titulo VARCHAR(255) NOT NULL,
                    title VARCHAR(255) NOT NULL,
                    descricao TEXT,
                    objetivo TEXT,
                    program VARCHAR(255),
                    indicadores JSONB DEFAULT '[]',
                    status VARCHAR(50) DEFAULT 'ativo',
                    created_by VARCHAR(255),
                    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
                )",
            'actions_unified' => "
                CREATE TABLE IF NOT EXISTS actions_unified (
                    id SERIAL PRIMARY KEY,
                    titulo VARCHAR(255) NOT NULL,
                    programa VARCHAR(255),
                    descricao TEXT,
                    responsavel VARCHAR(255),
                    status VARCHAR(50) DEFAULT 'pending',
                    created_by VARCHAR(255),
                    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
                )"
        ];
        
        foreach ($tables_to_create as $table_name => $sql) {
            $pdo->exec($sql);
            $actions[] = "Tabela $table_name criada/verificada";
        }
        
        // 4. Criar views para compatibilidade
        $pdo->exec("
            CREATE OR REPLACE VIEW metas_view AS 
            SELECT 
                id,
                COALESCE(titulo, title) as titulo,
                COALESCE(title, titulo) as title,
                descricao,
                objetivo,
                program,
                indicadores,
                status,
                created_by,
                created_at,
                updated_at
            FROM metas
        ");
        $actions[] = "View metas_view criada para compatibilidade";
        
        // 5. Teste de inserção para verificar se funciona
        $test_insert = "
            INSERT INTO metas (title, titulo, objetivo, status) 
            VALUES ('Teste de Estrutura', 'Teste de Estrutura', 'Verificar compatibilidade', 'ativo')
            RETURNING id
        ";
        $stmt = $pdo->prepare($test_insert);
        $stmt->execute();
        $test_id = $stmt->fetchColumn();
        
        // Verificar se o registro foi inserido
        $stmt = $pdo->prepare("SELECT * FROM metas WHERE id = ?");
        $stmt->execute([$test_id]);
        $test_record = $stmt->fetch();
        
        // Limpar teste
        $pdo->prepare("DELETE FROM metas WHERE id = ?")->execute([$test_id]);
        
        $results = [
            'status' => 'SUCCESS',
            'actions_performed' => $actions,
            'test_insert' => 'SUCCESS',
            'test_record' => $test_record
        ];
        
    } catch (Exception $e) {
        $results = [
            'status' => 'FAILED',
            'error' => $e->getMessage(),
            'actions_performed' => $actions,
            'trace' => $e->getTraceAsString()
        ];
    }
    
    return $results;
}

function runFullDiagnosticAndFix($pdo) {
    $diagnostic = [
        'timestamp' => date('Y-m-d H:i:s'),
        'step_1_analyze' => analyzeCurrentStructure($pdo),
        'step_2_fix' => fixDatabaseStructure($pdo),
        'step_3_verify' => analyzeCurrentStructure($pdo)
    ];
    
    // Teste final da API
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as metas_count FROM metas");
        $metas_count = $stmt->fetchColumn();
        
        $stmt = $pdo->query("SELECT COUNT(*) as actions_count FROM actions");
        $actions_count = $stmt->fetchColumn();
        
        $diagnostic['final_verification'] = [
            'database_accessible' => true,
            'metas_count' => $metas_count,
            'actions_count' => $actions_count,
            'api_ready' => true
        ];
        
    } catch (Exception $e) {
        $diagnostic['final_verification'] = [
            'database_accessible' => false,
            'error' => $e->getMessage()
        ];
    }
    
    return $diagnostic;
}

// Executar diagnóstico e correção
try {
    $pdo = getDbConnection();
    
    $action = $_GET['action'] ?? 'full';
    
    switch ($action) {
        case 'analyze':
            $result = analyzeCurrentStructure($pdo);
            break;
            
        case 'fix':
            $result = fixDatabaseStructure($pdo);
            break;
            
        case 'full':
        default:
            $result = runFullDiagnosticAndFix($pdo);
            break;
    }
    
    echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'FAILED',
        'error' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
?>
