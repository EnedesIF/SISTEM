<?php
// api.php - Versão corrigida para PostgreSQL Render
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

function connectDatabase() {
    try {
        $database_url = getenv('DATABASE_URL');
        
        if (!$database_url) {
            throw new Exception('DATABASE_URL não encontrada');
        }
        
        // Parse da URL
        $db_parts = parse_url($database_url);
        
        if (!$db_parts || !isset($db_parts['host'])) {
            throw new Exception('URL do banco inválida');
        }
        
        $host = $db_parts['host'];
        $port = $db_parts['port'] ?? 5432;
        $user = $db_parts['user'];
        $pass = $db_parts['pass'];
        $dbname = ltrim($db_parts['path'], '/');
        
        // DSN limpo para PostgreSQL
        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";
        
        // Opções específicas para PostgreSQL - SEM autocommit
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => 30
        ];
        
        $pdo = new PDO($dsn, $user, $pass, $options);
        
        // Configurações PostgreSQL após conexão
        $pdo->exec("SET timezone = 'UTC'");
        
        // Teste de conexão
        $pdo->query('SELECT 1');
        
        return $pdo;
        
    } catch (PDOException $e) {
        error_log("PostgreSQL Error: " . $e->getMessage());
        throw new Exception("Erro PostgreSQL: " . $e->getMessage());
    } catch (Exception $e) {
        error_log("Database Error: " . $e->getMessage());
        throw new Exception("Erro de conexão: " . $e->getMessage());
    }
}

function logResponse($operation, $success, $data = null, $error = null) {
    $log = [
        'timestamp' => date('Y-m-d H:i:s'),
        'operation' => $operation,
        'success' => $success
    ];
    
    if ($data) $log['data'] = $data;
    if ($error) $log['error'] = $error;
    
    error_log(json_encode($log));
    return $log;
}

// Roteamento
$endpoint = $_GET['endpoint'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($endpoint) {
        case 'test':
            $db = connectDatabase();
            
            $response = [
                'status' => 'success',
                'message' => 'Conexão PostgreSQL funcionando!',
                'timestamp' => date('Y-m-d H:i:s'),
                'database' => 'connected'
            ];
            
            // Verificar versão do PostgreSQL
            $version = $db->query('SELECT version()')->fetchColumn();
            $response['postgresql_version'] = substr($version, 0, 100);
            
            echo json_encode($response);
            logResponse('TEST_CONNECTION', true, ['database' => 'connected']);
            break;
            
        case 'setup':
            $db = connectDatabase();
            
            // Criar tabelas se não existirem
            $sql = "
                CREATE TABLE IF NOT EXISTS metas (
                    id SERIAL PRIMARY KEY,
                    title VARCHAR(255) NOT NULL,
                    titulo VARCHAR(255),
                    objetivo TEXT,
                    status VARCHAR(50) DEFAULT 'ativo',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                );
                
                CREATE TABLE IF NOT EXISTS actions (
                    id SERIAL PRIMARY KEY,
                    descricao TEXT NOT NULL,
                    status VARCHAR(50) DEFAULT 'pendente',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                );
                
                CREATE TABLE IF NOT EXISTS cronograma (
                    id SERIAL PRIMARY KEY,
                    atividade TEXT NOT NULL,
                    data_inicio DATE,
                    data_fim DATE,
                    status VARCHAR(50) DEFAULT 'planejado',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                );
                
                -- Garantir compatibilidade
                DO $$ 
                BEGIN 
                    IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_name='metas' AND column_name='titulo') THEN
                        ALTER TABLE metas ADD COLUMN titulo VARCHAR(255);
                        UPDATE metas SET titulo = title WHERE titulo IS NULL;
                    END IF;
                END $$;
            ";
            
            $db->exec($sql);
            
            // Verificar tabelas criadas
            $tables = $db->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' ORDER BY table_name")->fetchAll(PDO::FETCH_COLUMN);
            
            echo json_encode([
                'success' => true,
                'message' => 'Estrutura do banco configurada',
                'tables' => $tables
            ]);
            
            logResponse('SETUP_DATABASE', true, ['tables' => count($tables)]);
            break;
            
        case 'metas':
            $db = connectDatabase();
            
            if ($method === 'GET') {
                $stmt = $db->query('SELECT * FROM metas ORDER BY id DESC LIMIT 50');
                $metas = $stmt->fetchAll();
                
                echo json_encode([
                    'success' => true,
                    'data' => $metas,
                    'count' => count($metas)
                ]);
                
            } elseif ($method === 'POST') {
                $input = json_decode(file_get_contents('php://input'), true);
                
                if (!$input || !isset($input['title'])) {
                    throw new Exception('Título é obrigatório');
                }
                
                $stmt = $db->prepare('INSERT INTO metas (title, titulo, objetivo, status) VALUES (?, ?, ?, ?) RETURNING id');
                $stmt->execute([
                    $input['title'],
                    $input['title'],
                    $input['objetivo'] ?? '',
                    $input['status'] ?? 'ativo'
                ]);
                
                $result = $stmt->fetch();
                $id = $result['id'];
                
                echo json_encode([
                    'success' => true,
                    'id' => $id,
                    'message' => 'Meta criada com sucesso'
                ]);
            }
            break;
            
        case 'health':
            $db = connectDatabase();
            
            $health = [
                'status' => 'healthy',
                'timestamp' => date('Y-m-d H:i:s'),
                'services' => [
                    'api' => 'running',
                    'database' => 'connected',
                    'postgresql' => 'active'
                ]
            ];
            
            // Contar registros nas tabelas principais
            try {
                $metas_count = $db->query("SELECT COUNT(*) FROM metas")->fetchColumn();
                $health['data'] = ['metas' => $metas_count];
            } catch (Exception $e) {
                $health['data'] = ['note' => 'Tabelas não criadas ainda'];
            }
            
            echo json_encode($health);
            break;
            
        default:
            http_response_code(404);
            echo json_encode([
                'error' => 'Endpoint não encontrado',
                'available' => ['test', 'setup', 'metas', 'health']
            ]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    $error_response = [
        'error' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s'),
        'endpoint' => $endpoint
    ];
    
    echo json_encode($error_response);
    logResponse('ERROR', false, null, $e->getMessage());
}
?>
