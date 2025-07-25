<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

class ENEDESApi {
    private $pdo;
    
    public function __construct() {
        $this->connectDatabase();
    }
    
    private function connectDatabase() {
        try {
            $database_url = $_ENV['DATABASE_URL'] ?? '';
            if (empty($database_url)) {
                throw new Exception('DATABASE_URL não configurada');
            }
            
            $parsed = parse_url($database_url);
            
            $host = $parsed['host'] ?? '';
            $port = $parsed['port'] ?? 5432;
            $database = ltrim($parsed['path'] ?? '', '/');
            $username = $parsed['user'] ?? '';
            $password = $parsed['pass'] ?? '';
            
            $query_params = [];
            if (isset($parsed['query'])) {
                parse_str($parsed['query'], $query_params);
            }
            $sslmode = $query_params['sslmode'] ?? 'require';
            
            $dsn = "pgsql:host={$host};port={$port};dbname={$database};sslmode={$sslmode}";
            
            $this->pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_TIMEOUT => 30
            ]);
            
        } catch (Exception $e) {
            $this->sendError('Erro de conexão com banco: ' . $e->getMessage());
        }
    }
    
    public function handleRequest() {
        $endpoint = $_GET['endpoint'] ?? '';
        $method = $_SERVER['REQUEST_METHOD'];
        
        try {
            switch ($endpoint) {
                case 'test':
                    return $this->testConnection();
                case 'metas':
                    return $this->handleMetas($method);
                case 'followups':
                    return $this->handleFollowups($method);
                case 'cronograma':
                    return $this->handleCronograma($method);
                case 'programas':
                    return $this->handleProgramas($method);
                case 'dashboard':
                    return $this->getDashboardData();
                case 'inventario':
                    return $this->handleInventario($method);
                case 'etapas':
                    return $this->handleEtapas($method);
                case 'acoes':
                    return $this->handleAcoes($method);
                case 'tarefas':
                    return $this->handleTarefas($method);
                default:
                    return $this->sendError('Endpoint não encontrado: ' . $endpoint);
            }
        } catch (Exception $e) {
            return $this->sendError('Erro interno: ' . $e->getMessage());
        }
    }
    
    private function testConnection() {
        try {
            $stmt = $this->pdo->query("SELECT version(), current_database(), current_user, NOW() as timestamp");
            $info = $stmt->fetch();
            
            $stmt = $this->pdo->query("
                SELECT table_name, 
                       (SELECT COUNT(*) FROM information_schema.columns 
                        WHERE table_name = t.table_name AND table_schema = 'public') as column_count
                FROM information_schema.tables t
                WHERE table_schema = 'public' 
                ORDER BY table_name
            ");
            $tables = $stmt->fetchAll();
            
            return $this->sendSuccess([
                'message' => 'API conectada ao PostgreSQL com estrutura real!',
                'connection_info' => [
                    'driver' => 'PDO PostgreSQL',
                    'status' => 'connected',
                    'database' => $info['current_database'],
                    'user' => $info['current_user'],
                    'server_version' => $info['version'],
                    'timestamp' => $info['timestamp']
                ],
                'database_structure' => $tables
            ]);
            
        } catch (PDOException $e) {
            return $this->sendError('Erro no teste de conexão: ' . $e->getMessage());
        }
    }
    
    private function handleMetas($method) {
        try {
            if ($method === 'POST') {
                $input = $this->getJsonInput();
                
                // Verificar se existe programa ou criar
                $programa_id = $this->getOrCreatePrograma($input['programa'] ?? 'Programa Padrão');
                
                $stmt = $this->pdo->prepare("
                    INSERT INTO metas (nome, descricao, programa_id) 
                    VALUES (?, ?, ?) 
                    RETURNING id
                ");
                $stmt->execute([
                    $input['descricao'] ?? 'Nova Meta',
                    $input['descricao'] ?? '',
                    $programa_id
                ]);
                
                $meta_id = $stmt->fetchColumn();
                
                return $this->sendSuccess([
                    'message' => 'Meta adicionada com sucesso',
                    'id' => $meta_id,
                    'data' => $input
                ]);
                
            } else {
                $stmt = $this->pdo->query("
                    SELECT m.*, p.nome as programa_nome 
                    FROM metas m 
                    LEFT JOIN programas p ON m.programa_id = p.id 
                    ORDER BY m.id DESC 
                    LIMIT 50
                ");
                $metas = $stmt->fetchAll();
                
                return $this->sendSuccess(['data' => $metas]);
            }
            
        } catch (PDOException $e) {
            return $this->sendError('Erro nas metas: ' . $e->getMessage());
        }
    }
    
    private function handleFollowups($method) {
        try {
            if ($method === 'POST') {
                $input = $this->getJsonInput();
                
                // Adaptar dados do frontend para estrutura real
                // Frontend envia: programa, acao, status, prazo, observacoes
                // Banco espera: tarefa_id, usuario_id, data, comentario
                
                $stmt = $this->pdo->prepare("
                    INSERT INTO followups (tarefa_id, usuario_id, data, comentario) 
                    VALUES (?, ?, ?, ?) 
                    RETURNING id
                ");
                
                $stmt->execute([
                    null, // tarefa_id - pode ser null por enquanto
                    1, // usuario_id padrão
                    $input['prazo'] ?? date('Y-m-d'),
                    json_encode([
                        'programa' => $input['programa'] ?? '',
                        'acao' => $input['acao'] ?? '',
                        'status' => $input['status'] ?? '',
                        'observacoes' => $input['observacoes'] ?? ''
                    ])
                ]);
                
                $followup_id = $stmt->fetchColumn();
                
                return $this->sendSuccess([
                    'message' => 'Follow-up adicionado com sucesso',
                    'id' => $followup_id,
                    'data' => $input
                ]);
                
            } else {
                $stmt = $this->pdo->query("
                    SELECT *, 
                           comentario as dados_originais
                    FROM followups 
                    ORDER BY id DESC 
                    LIMIT 50
                ");
                $followups = $stmt->fetchAll();
                
                // Adaptar dados para o frontend
                foreach ($followups as &$followup) {
                    if ($followup['comentario']) {
                        $dados = json_decode($followup['comentario'], true);
                        if ($dados) {
                            $followup['programa'] = $dados['programa'] ?? 'N/A';
                            $followup['acao'] = $dados['acao'] ?? 'N/A';
                            $followup['status'] = $dados['status'] ?? 'N/A';
                            $followup['observacoes'] = $dados['observacoes'] ?? '';
                        }
                    }
                    $followup['prazo'] = $followup['data'];
                }
                
                return $this->sendSuccess(['data' => $followups]);
            }
            
        } catch (PDOException $e) {
            return $this->sendError('Erro nos follow-ups: ' . $e->getMessage());
        }
    }
    
    private function handleCronograma($method) {
        try {
            if ($method === 'POST') {
                $input = $this->getJsonInput();
                
                // Adaptar dados do frontend para estrutura real
                // Frontend envia: programa, descricao, valor, data_prevista, observacoes
                // Banco espera: meta_id, etapa, inicio, prazo_final, rubrica, valor_executado
                
                $stmt = $this->pdo->prepare("
                    INSERT INTO cronograma (meta_id, etapa, inicio, prazo_final, rubrica, valor_executado) 
                    VALUES (?, ?, ?, ?, ?, ?) 
                    RETURNING id
                ");
                
                $stmt->execute([
                    null, // meta_id - pode ser null
                    $input['descricao'] ?? 'Etapa não especificada',
                    date('Y-m-d'),
                    $input['data_prevista'] ?? date('Y-m-d'),
                    $input['programa'] ?? 'Programa não especificado',
                    $input['valor'] ?? 0
                ]);
                
                $cronograma_id = $stmt->fetchColumn();
                
                return $this->sendSuccess([
                    'message' => 'Item do cronograma adicionado com sucesso',
                    'id' => $cronograma_id,
                    'data' => $input
                ]);
                
            } else {
                $stmt = $this->pdo->query("
                    SELECT *,
                           etapa as descricao,
                           rubrica as programa,
                           prazo_final as data_prevista,
                           valor_executado as valor,
                           'Observações do cronograma' as observacoes
                    FROM cronograma 
                    ORDER BY prazo_final ASC 
                    LIMIT 50
                ");
                $cronograma = $stmt->fetchAll();
                
                return $this->sendSuccess(['data' => $cronograma]);
            }
            
        } catch (PDOException $e) {
            return $this->sendError('Erro no cronograma: ' . $e->getMessage());
        }
    }
    
    private function handleProgramas($method) {
        try {
            if ($method === 'GET') {
                $stmt = $this->pdo->query("SELECT * FROM programas ORDER BY nome");
                $programas = $stmt->fetchAll();
                
                // Se não há programas, criar os padrões
                if (empty($programas)) {
                    $programas_padrao = [
                        'Programa 1 - Expansão Acadêmica',
                        'Programa 2 - Inovação Tecnológica',
                        'Programa 3 - Sustentabilidade',
                        'Programa 4 - Internacionalização',
                        'Programa 5 - Pesquisa Aplicada',
                        'Programa 6 - Extensão Comunitária',
                        'Programa 7 - Gestão Institucional',
                        'Programa 8 - Infraestrutura'
                    ];
                    
                    foreach ($programas_padrao as $nome) {
                        $this->getOrCreatePrograma($nome);
                    }
                    
                    // Buscar novamente
                    $stmt = $this->pdo->query("SELECT * FROM programas ORDER BY nome");
                    $programas = $stmt->fetchAll();
                }
                
                return $this->sendSuccess(['data' => $programas]);
            }
            
            return $this->sendError('Método não suportado para programas');
            
        } catch (PDOException $e) {
            return $this->sendError('Erro nos programas: ' . $e->getMessage());
        }
    }
    
    private function handleEtapas($method) {
        try {
            if ($method === 'GET') {
                $stmt = $this->pdo->query("
                    SELECT e.*, m.nome as meta_nome 
                    FROM etapas e 
                    LEFT JOIN metas m ON e.meta_id = m.id 
                    ORDER BY e.id DESC 
                    LIMIT 50
                ");
                $etapas = $stmt->fetchAll();
                
                return $this->sendSuccess(['data' => $etapas]);
            }
            
        } catch (PDOException $e) {
            return $this->sendError('Erro nas etapas: ' . $e->getMessage());
        }
    }
    
    private function handleAcoes($method) {
        try {
            if ($method === 'GET') {
                $stmt = $this->pdo->query("
                    SELECT a.*, e.nome as etapa_nome 
                    FROM acoes a 
                    LEFT JOIN etapas e ON a.etapa_id = e.id 
                    ORDER BY a.id DESC 
                    LIMIT 50
                ");
                $acoes = $stmt->fetchAll();
                
                return $this->sendSuccess(['data' => $acoes]);
            }
            
        } catch (PDOException $e) {
            return $this->sendError('Erro nas ações: ' . $e->getMessage());
        }
    }
    
    private function handleTarefas($method) {
        try {
            if ($method === 'GET') {
                $stmt = $this->pdo->query("
                    SELECT t.*, a.nome as acao_nome 
                    FROM tarefas t 
                    LEFT JOIN acoes a ON t.acao_id = a.id 
                    ORDER BY t.id DESC 
                    LIMIT 50
                ");
                $tarefas = $stmt->fetchAll();
                
                return $this->sendSuccess(['data' => $tarefas]);
            }
            
        } catch (PDOException $e) {
            return $this->sendError('Erro nas tarefas: ' . $e->getMessage());
        }
    }
    
    private function handleInventario($method) {
        try {
            // Criar tabela de inventário (não existe na estrutura atual)
            $this->pdo->exec("
                CREATE TABLE IF NOT EXISTS inventario (
                    id SERIAL PRIMARY KEY,
                    programa VARCHAR(255) NOT NULL,
                    equipamento VARCHAR(255) NOT NULL,
                    quantidade INTEGER NOT NULL DEFAULT 1,
                    valor_unitario DECIMAL(15,2) NOT NULL DEFAULT 0,
                    observacoes TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            ");
            
            if ($method === 'POST') {
                $input = $this->getJsonInput();
                
                $stmt = $this->pdo->prepare("
                    INSERT INTO inventario (programa, equipamento, quantidade, valor_unitario, observacoes) 
                    VALUES (?, ?, ?, ?, ?) 
                    RETURNING id
                ");
                
                $stmt->execute([
                    $input['programa'] ?? 'Programa não especificado',
                    $input['equipamento'] ?? '',
                    $input['quantidade'] ?? 1,
                    $input['valor_unitario'] ?? 0,
                    $input['observacoes'] ?? ''
                ]);
                
                $inventario_id = $stmt->fetchColumn();
                
                return $this->sendSuccess([
                    'message' => 'Item do inventário adicionado com sucesso',
                    'id' => $inventario_id,
                    'data' => $input
                ]);
                
            } else {
                $stmt = $this->pdo->query("SELECT * FROM inventario ORDER BY created_at DESC LIMIT 50");
                $inventario = $stmt->fetchAll();
                
                return $this->sendSuccess(['data' => $inventario]);
            }
            
        } catch (PDOException $e) {
            return $this->sendError('Erro no inventário: ' . $e->getMessage());
        }
    }
    
    private function getDashboardData() {
        try {
            $dashboard = [];
            
            // Contar registros das tabelas principais
            $tabelas = ['metas', 'followups', 'cronograma', 'programas', 'etapas', 'acoes', 'tarefas'];
            foreach ($tabelas as $tabela) {
                try {
                    $stmt = $this->pdo->query("SELECT COUNT(*) FROM {$tabela}");
                    $dashboard["total_{$tabela}"] = $stmt->fetchColumn();
                } catch (PDOException $e) {
                    $dashboard["total_{$tabela}"] = 0;
                }
            }
            
            // Taxa de execução simulada
            $dashboard['taxa_execucao'] = 78.5;
            
            return $this->sendSuccess(['data' => $dashboard]);
            
        } catch (PDOException $e) {
            return $this->sendError('Erro no dashboard: ' . $e->getMessage());
        }
    }
    
    private function getOrCreatePrograma($nome) {
        $stmt = $this->pdo->prepare("SELECT id FROM programas WHERE nome = ? LIMIT 1");
        $stmt->execute([$nome]);
        $programa_id = $stmt->fetchColumn();
        
        if (!$programa_id) {
            $stmt = $this->pdo->prepare("INSERT INTO programas (nome, descricao) VALUES (?, ?) RETURNING id");
            $stmt->execute([$nome, "Descrição automática para {$nome}"]);
            $programa_id = $stmt->fetchColumn();
        }
        
        return $programa_id;
    }
    
    private function getJsonInput() {
        $input = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('JSON inválido: ' . json_last_error_msg());
        }
        return $input ?: [];
    }
    
    private function sendSuccess($data) {
        echo json_encode([
            'status' => 'success',
            'timestamp' => date('Y-m-d H:i:s'),
            ...$data
        ]);
        exit;
    }
    
    private function sendError($message) {
        echo json_encode([
            'status' => 'error',
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        exit;
    }
}

// Executar API
try {
    $api = new ENEDESApi();
    $api->handleRequest();
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Erro crítico: ' . $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
?>
