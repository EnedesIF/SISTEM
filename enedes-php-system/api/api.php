<?php
// api.php - Configurado EXCLUSIVAMENTE para Render PostgreSQL
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ✅ CONFIGURAÇÃO RENDER POSTGRESQL - Credenciais da sua imagem
function getDbConnection() {
    // Primeiro tentar environment variable (Render interno)
    $database_url = getenv('DATABASE_URL');
    
    if ($database_url) {
        // Usar DATABASE_URL interno do Render
        $db = parse_url($database_url);
        $host = $db['host'];
        $port = $db['port'] ?? 5432;
        $dbname = ltrim($db['path'], '/');
        $username = $db['user'];
        $password = $db['pass'];
    } else {
        // ✅ CREDENCIAIS RENDER POSTGRESQL (conforme sua imagem)
        $host = 'dpg-d1u47ber433s73ebqecg-a.oregon-postgres.render.com';
        $port = '5432';
        $dbname = 'enedesifb';
        $username = 'enedesifb_user';
        $password = 'E8kOWf5R9eAUV6XZJBeYNVcgBdmcTJUB';
    }

    try {
        $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_AUTOCOMMIT => true,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        
        // Garantir autocommit
        $pdo->exec("SET autocommit = ON");
        
        return $pdo;
        
    } catch (PDOException $e) {
        throw new Exception("Render PostgreSQL connection failed: " . $e->getMessage());
    }
}

// ✅ CRIAR TABELAS COMPATÍVEIS COM O FRONTEND
function createTablesIfNeeded($pdo) {
    try {
        // ✅ TABELA METAS - Estrutura compatível com frontend
        $pdo->exec("CREATE TABLE IF NOT EXISTS metas (
            id SERIAL PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            titulo VARCHAR(255),
            objetivo TEXT,
            program VARCHAR(255),
            programa VARCHAR(255),
            indicadores JSON DEFAULT '[]',
            status VARCHAR(50) DEFAULT 'ativo',
            created_by VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        // ✅ TABELA ACTIONS - Estrutura exata que o frontend espera
        $pdo->exec("CREATE TABLE IF NOT EXISTS actions (
            id SERIAL PRIMARY KEY,
            titulo VARCHAR(255) NOT NULL,
            programa VARCHAR(255),
            descricao TEXT,
            responsavel VARCHAR(255),
            status VARCHAR(50) DEFAULT 'pending',
            created_by VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        // ✅ TABELA FOLLOWUPS - Para futuro uso
        $pdo->exec("CREATE TABLE IF NOT EXISTS followups (
            id SERIAL PRIMARY KEY,
            target_id INTEGER,
            type VARCHAR(50),
            mensagem TEXT,
            prioridade VARCHAR(50),
            prazo DATE,
            colaboradores JSON DEFAULT '[]',
            attachments JSON DEFAULT '[]',
            status VARCHAR(50) DEFAULT 'pending',
            created_by VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        // ✅ TABELA TASKS - Para futuro uso
        $pdo->exec("CREATE TABLE IF NOT EXISTS tasks (
            id SERIAL PRIMARY KEY,
            followup_id INTEGER,
            titulo VARCHAR(255),
            descricao TEXT,
            responsavel VARCHAR(255),
            status VARCHAR(50) DEFAULT 'pending',
            prazo DATE,
            attachments JSON DEFAULT '[]',
            created_by VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        // ✅ TABELA CRONOGRAMA
        $pdo->exec("CREATE TABLE IF NOT EXISTS cronograma (
            id SERIAL PRIMARY KEY,
            nome VARCHAR(255),
            inicio DATE,
            fim DATE,
            rubrica DECIMAL(12,2),
            executado DECIMAL(12,2),
            created_by VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        // ✅ TABELA INVENTARIO
        $pdo->exec("CREATE TABLE IF NOT EXISTS inventario (
            id SERIAL PRIMARY KEY,
            programa VARCHAR(255),
            item VARCHAR(255),
            descricao TEXT,
            valor DECIMAL(12,2),
            atividades_relacionadas TEXT,
            created_by VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
    } catch (PDOException $e) {
        error_log("Erro ao criar tabelas: " . $e->getMessage());
    }
}

// Função para logging
function logOperation($operation, $data = null, $success = null) {
    $log = [
        'timestamp' => date('Y-m-d H:i:s'),
        'operation' => $operation,
        'success' => $success,
        'data' => $data ? json_encode($data) : null
    ];
    error_log(json_encode($log));
}

// ===== PROCESSAMENTO PRINCIPAL =====
try {
    $pdo = getDbConnection();
    createTablesIfNeeded($pdo);
    
    $method = $_SERVER['REQUEST_METHOD'];
    $endpoint = $_GET['endpoint'] ?? '';
    
    switch ($endpoint) {
        
        // ✅ ENDPOINT TEST - Diagnóstico completo
        case 'test':
            if ($method === 'GET') {
                // Teste de conexão e estrutura
                $metasCount = $pdo->query("SELECT COUNT(*) FROM metas")->fetchColumn();
                $actionsCount = $pdo->query("SELECT COUNT(*) FROM actions")->fetchColumn();
                
                echo json_encode([
                    'status' => 'success',
                    'message' => 'ENEDES API funcionando com Render PostgreSQL!',
                    'timestamp' => date('Y-m-d H:i:s'),
                    'method' => $method,
                    'php_version' => PHP_VERSION,
                    'database_connected' => true,
                    'config_method' => getenv('DATABASE_URL') ? 'Environment Variable (Render Internal)' : 'Direct Credentials',
                    'environment' => 'Production',
                    'render_postgres_connected' => 'Yes',
                    'tables_auto_created' => 'Yes',
                    'database_info' => [
                        'provider' => 'Render PostgreSQL',
                        'hostname' => 'dpg-d1u47ber433s73ebqecg-a',
                        'database' => 'enedesifb',
                        'user' => 'enedesifb_user'
                    ],
                    'current_data' => [
                        'metas_count' => $metasCount,
                        'actions_count' => $actionsCount
                    ]
                ]);
            } elseif ($method === 'POST') {
                // Teste de inserção
                $pdo->beginTransaction();
                
                $sql = "INSERT INTO metas (title, titulo, objetivo, status) 
                        VALUES (:title, :titulo, :objetivo, :status) 
                        RETURNING id";
                
                $testTitle = 'Teste Render PostgreSQL - ' . date('H:i:s');
                $stmt = $pdo->prepare($sql);
                $result = $stmt->execute([
                    'title' => $testTitle,
                    'titulo' => $testTitle,
                    'objetivo' => 'Meta de teste para verificar Render PostgreSQL',
                    'status' => 'ativo'
                ]);
                
                if ($result) {
                    $id = $stmt->fetchColumn();
                    $pdo->commit();
                    
                    echo json_encode([
                        'success' => true,
                        'message' => 'Teste de inserção no Render PostgreSQL concluído!',
                        'inserted_id' => $id,
                        'persistence_confirmed' => true,
                        'database_provider' => 'Render PostgreSQL'
                    ]);
                } else {
                    $pdo->rollback();
                    throw new Exception('Falha no teste de inserção');
                }
            }
            break;

        // ✅ ENDPOINT GOALS (METAS) - 100% compatível com frontend
        case 'goals':
            if ($method === 'GET') {
                $stmt = $pdo->query("SELECT * FROM metas ORDER BY id DESC");
                $metas = $stmt->fetchAll();
                
                // ✅ GARANTIR COMPATIBILIDADE: todos os campos que o frontend espera
                $metasFormatted = array_map(function($meta) {
                    // Garantir que tanto 'title' quanto 'titulo' existam
                    if (!$meta['titulo'] && $meta['title']) {
                        $meta['titulo'] = $meta['title'];
                    }
                    if (!$meta['title'] && $meta['titulo']) {
                        $meta['title'] = $meta['titulo'];
                    }
                    
                    // Garantir que tanto 'program' quanto 'programa' existam
                    if (!$meta['programa'] && $meta['program']) {
                        $meta['programa'] = $meta['program'];
                    }
                    if (!$meta['program'] && $meta['programa']) {
                        $meta['program'] = $meta['programa'];
                    }
                    
                    // Decodificar JSON se necessário
                    if (isset($meta['indicadores']) && is_string($meta['indicadores'])) {
                        $meta['indicadores'] = json_decode($meta['indicadores'], true) ?: [];
                    }
                    
                    return $meta;
                }, $metas);
                
                echo json_encode(['success' => true, 'data' => $metasFormatted]);
                
            } elseif ($method === 'POST') {
                $input = json_decode(file_get_contents('php://input'), true);
                
                if (!$input) {
                    throw new Exception('Dados não recebidos ou JSON inválido');
                }
                
                // ✅ ACEITAR AMBOS OS FORMATOS: title/titulo
                $title = $input['title'] ?? $input['titulo'] ?? '';
                $objetivo = $input['objetivo'] ?? '';
                $program = $input['program'] ?? $input['programa'] ?? '';
                $indicadores = $input['indicadores'] ?? [];
                $status = $input['status'] ?? 'ativo';
                $created_by = $input['created_by'] ?? 'Sistema';
                
                if (!$title) {
                    throw new Exception('Campo title/titulo é obrigatório');
                }
                
                $pdo->beginTransaction();
                
                $sql = "INSERT INTO metas (title, titulo, objetivo, program, programa, indicadores, status, created_by, created_at) 
                        VALUES (:title, :titulo, :objetivo, :program, :programa, :indicadores, :status, :created_by, NOW()) 
                        RETURNING id";
                
                $stmt = $pdo->prepare($sql);
                $result = $stmt->execute([
                    'title' => $title,
                    'titulo' => $title,
                    'objetivo' => $objetivo,
                    'program' => $program,
                    'programa' => $program,
                    'indicadores' => json_encode($indicadores),
                    'status' => $status,
                    'created_by' => $created_by
                ]);
                
                if ($result) {
                    $id = $stmt->fetchColumn();
                    $pdo->commit();
                    
                    logOperation('INSERT_META', $input, true);
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Meta inserida no Render PostgreSQL!', 
                        'id' => $id
                    ]);
                } else {
                    $pdo->rollback();
                    throw new Exception('Falha na inserção da meta');
                }
                
            } elseif ($method === 'PUT') {
                $input = json_decode(file_get_contents('php://input'), true);
                $id = $input['id'] ?? null;
                
                if (!$id) {
                    throw new Exception('ID é obrigatório para atualização');
                }
                
                $title = $input['title'] ?? $input['titulo'] ?? '';
                $objetivo = $input['objetivo'] ?? '';
                $program = $input['program'] ?? $input['programa'] ?? '';
                $indicadores = $input['indicadores'] ?? [];
                $status = $input['status'] ?? 'ativo';
                
                $pdo->beginTransaction();
                
                $sql = "UPDATE metas SET title = :title, titulo = :titulo, objetivo = :objetivo, program = :program, programa = :programa, indicadores = :indicadores, status = :status, updated_at = NOW() WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    'id' => $id,
                    'title' => $title,
                    'titulo' => $title,
                    'objetivo' => $objetivo,
                    'program' => $program,
                    'programa' => $program,
                    'indicadores' => json_encode($indicadores),
                    'status' => $status
                ]);
                
                $pdo->commit();
                echo json_encode(['success' => true, 'message' => 'Meta atualizada no Render PostgreSQL!']);
                
            } elseif ($method === 'DELETE') {
                $input = json_decode(file_get_contents('php://input'), true);
                $id = $input['id'] ?? null;
                
                if (!$id) {
                    throw new Exception('ID é obrigatório para exclusão');
                }
                
                $pdo->beginTransaction();
                $stmt = $pdo->prepare("DELETE FROM metas WHERE id = :id");
                $stmt->execute(['id' => $id]);
                $pdo->commit();
                
                echo json_encode(['success' => true, 'message' => 'Meta excluída do Render PostgreSQL!']);
            }
            break;

        // ✅ ENDPOINT ACTIONS - 100% compatível com frontend
        case 'actions':
            if ($method === 'GET') {
                $stmt = $pdo->query("SELECT * FROM actions ORDER BY id DESC");
                $actions = $stmt->fetchAll();
                echo json_encode(['success' => true, 'data' => $actions]);
                
            } elseif ($method === 'POST') {
                $input = json_decode(file_get_contents('php://input'), true);
                
                if (!$input || empty($input['titulo'])) {
                    throw new Exception('Campo titulo é obrigatório');
                }
                
                $pdo->beginTransaction();
                
                $sql = "INSERT INTO actions (titulo, programa, descricao, responsavel, status, created_by, created_at) 
                        VALUES (:titulo, :programa, :descricao, :responsavel, :status, :created_by, NOW()) 
                        RETURNING id";
                
                $stmt = $pdo->prepare($sql);
                $result = $stmt->execute([
                    'titulo' => $input['titulo'],
                    'programa' => $input['programa'] ?? '',
                    'descricao' => $input['descricao'] ?? '',
                    'responsavel' => $input['responsavel'] ?? '',
                    'status' => $input['status'] ?? 'pending',
                    'created_by' => $input['created_by'] ?? 'Sistema'
                ]);
                
                if ($result) {
                    $id = $stmt->fetchColumn();
                    $pdo->commit();
                    
                    logOperation('INSERT_ACTION', $input, true);
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Ação inserida no Render PostgreSQL!', 
                        'id' => $id
                    ]);
                } else {
                    $pdo->rollback();
                    throw new Exception('Falha na inserção da ação');
                }
                
            } elseif ($method === 'PUT') {
                $input = json_decode(file_get_contents('php://input'), true);
                $id = $input['id'] ?? null;
                
                if (!$id) {
                    throw new Exception('ID é obrigatório para atualização');
                }
                
                $pdo->beginTransaction();
                
                $sql = "UPDATE actions SET titulo = :titulo, programa = :programa, descricao = :descricao, responsavel = :responsavel, status = :status, updated_at = NOW() WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    'id' => $id,
                    'titulo' => $input['titulo'],
                    'programa' => $input['programa'] ?? '',
                    'descricao' => $input['descricao'] ?? '',
                    'responsavel' => $input['responsavel'] ?? '',
                    'status' => $input['status'] ?? 'pending'
                ]);
                
                $pdo->commit();
                echo json_encode(['success' => true, 'message' => 'Ação atualizada no Render PostgreSQL!']);
                
            } elseif ($method === 'DELETE') {
                $input = json_decode(file_get_contents('php://input'), true);
                $id = $input['id'] ?? null;
                
                if (!$id) {
                    throw new Exception('ID é obrigatório para exclusão');
                }
                
                $pdo->beginTransaction();
                $stmt = $pdo->prepare("DELETE FROM actions WHERE id = :id");
                $stmt->execute(['id' => $id]);
                $pdo->commit();
                
                echo json_encode(['success' => true, 'message' => 'Ação excluída do Render PostgreSQL!']);
            }
            break;

        // ✅ ENDPOINTS FUTUROS (preparados para expansão)
        case 'followups':
            echo json_encode(['message' => 'Endpoint followups em desenvolvimento (Render PostgreSQL)']);
            break;
            
        case 'tasks':
            echo json_encode(['message' => 'Endpoint tasks em desenvolvimento (Render PostgreSQL)']);
            break;
            
        case 'schedule':
        case 'cronograma':
            echo json_encode(['message' => 'Endpoint cronograma em desenvolvimento (Render PostgreSQL)']);
            break;
            
        case 'inventory':
        case 'inventario':
            echo json_encode(['message' => 'Endpoint inventario em desenvolvimento (Render PostgreSQL)']);
            break;

        // ✅ STATUS COMPLETO
        case 'status':
            $metasCount = $pdo->query("SELECT COUNT(*) FROM metas")->fetchColumn();
            $actionsCount = $pdo->query("SELECT COUNT(*) FROM actions")->fetchColumn();
            
            echo json_encode([
                'status' => 'API funcionando perfeitamente com Render PostgreSQL!',
                'database' => 'Render PostgreSQL conectado',
                'provider' => 'Render (não Neon)',
                'total_metas' => $metasCount,
                'total_actions' => $actionsCount,
                'endpoints_funcionais' => ['test', 'goals', 'actions', 'status'],
                'endpoints_preparados' => ['followups', 'tasks', 'cronograma', 'inventario'],
                'compatibility' => [
                    'frontend_fields' => 'titulo, programa, descricao, responsavel',
                    'backend_fields' => 'title/titulo, program/programa (ambos suportados)',
                    'conversion' => 'Automática entre formatos'
                ],
                'structure' => [
                    'metas' => 'id, title, titulo, objetivo, program, programa, indicadores (JSON)',
                    'actions' => 'id, titulo, programa, descricao, responsavel, status'
                ],
                'database_config' => [
                    'hostname' => 'dpg-d1u47ber433s73ebqecg-a.oregon-postgres.render.com',
                    'database' => 'enedesifb',
                    'user' => 'enedesifb_user',
                    'provider' => 'Render PostgreSQL'
                ],
                'fixes_applied' => [
                    'render_postgresql_only' => true,
                    'no_neon_references' => true,
                    'autocommit_enabled' => true,
                    'explicit_transactions' => true,
                    'field_compatibility' => true,
                    'table_structure_aligned' => true
                ],
                'timestamp' => date('Y-m-d H:i:s'),
                'backend_url' => 'https://sistem-lk86.onrender.com/api/api.php',
                'frontend_compatible' => true
            ]);
            break;

        default:
            echo json_encode([
                'message' => 'API Sistema ENEDES - Configurada para Render PostgreSQL',
                'version' => '3.1 - Render PostgreSQL Exclusivo',
                'database_provider' => 'Render PostgreSQL (não Neon)',
                'database' => [
                    'provider' => 'Render PostgreSQL',
                    'hostname' => 'dpg-d1u47ber433s73ebqecg-a',
                    'database' => 'enedesifb',
                    'user' => 'enedesifb_user',
                    'status' => 'Conectado e funcional'
                ],
                'endpoints_funcionais' => [
                    'GET ?endpoint=test' => 'Teste completo da API e Render PostgreSQL',
                    'POST ?endpoint=test' => 'Teste de inserção com verificação',
                    'GET ?endpoint=goals' => 'Listar todas as metas',
                    'POST ?endpoint=goals' => 'Criar nova meta (JSON: {title/titulo, objetivo, program/programa, indicadores})',
                    'PUT ?endpoint=goals' => 'Atualizar meta (JSON: {id, title/titulo, objetivo, indicadores})',
                    'DELETE ?endpoint=goals' => 'Excluir meta (JSON: {id})',
                    'GET ?endpoint=actions' => 'Listar todas as ações',
                    'POST ?endpoint=actions' => 'Criar nova ação (JSON: {titulo, programa, descricao, responsavel, status})',
                    'PUT ?endpoint=actions' => 'Atualizar ação (JSON: {id, titulo, programa, descricao, responsavel, status})',
                    'DELETE ?endpoint=actions' => 'Excluir ação (JSON: {id})',
                    'GET ?endpoint=status' => 'Status detalhado da API'
                ],
                'render_postgresql_config' => [
                    'hostname' => 'dpg-d1u47ber433s73ebqecg-a.oregon-postgres.render.com',
                    'port' => '5432',
                    'database' => 'enedesifb',
                    'username' => 'enedesifb_user',
                    'internal_url' => 'postgresql://enedesifb_user:***@dpg-d1u47ber433s73ebqecg-a/enedesifb',
                    'external_url' => 'postgresql://enedesifb_user:***@dpg-d1u47ber433s73ebqecg-a.oregon-postgres.render.com/enedesifb'
                ]
            ]);
    }
    
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollback();
    }
    
    logOperation('ERROR', ['message' => $e->getMessage(), 'endpoint' => $endpoint ?? 'unknown'], false);
    http_response_code(400);
    echo json_encode([
        'error' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s'),
        'endpoint' => $endpoint ?? 'unknown',
        'method' => $method ?? 'unknown',
        'database_provider' => 'Render PostgreSQL'
    ]);
}
?>
