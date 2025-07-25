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
            
            $this->pdo = new PDO($database_url);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
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
                default:
                    return $this->sendError('Endpoint não encontrado: ' . $endpoint);
            }
        } catch (Exception $e) {
            return $this->sendError('Erro interno: ' . $e->getMessage());
        }
    }
    
    private function testConnection() {
        try {
            $stmt = $this->pdo->query("SELECT version(), current_database(), current_user, NOW()");
            $info = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verificar tabelas existentes
            $stmt = $this->pdo->query("
                SELECT table_name 
                FROM information_schema.tables 
                WHERE table_schema = 'public' 
                ORDER BY table_name
            ");
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            return $this->sendSuccess([
                'message' => 'API conectada ao PostgreSQL com sucesso!',
                'database_info' => $info,
                'tables' => $tables,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            
        } catch (PDOException $e) {
            return $this->sendError('Erro no teste: ' . $e->getMessage());
        }
    }
    
    private function handleMetas($method) {
        try {
            if ($method === 'POST') {
                $input = $this->getJsonInput();
                
                // Verificar se existe programa
                $stmt = $this->pdo->prepare("SELECT id FROM programas WHERE nome = ? LIMIT 1");
                $stmt->execute([$input['programa']]);
                $programa_id = $stmt->fetchColumn();
                
                if (!$programa_id) {
                    // Criar programa se não existir
                    $stmt = $this->pdo->prepare("INSERT INTO programas (nome, descricao) VALUES (?, ?) RETURNING id");
                    $stmt->execute([$input['programa'], 'Programa criado automaticamente']);
                    $programa_id = $stmt->fetchColumn();
                }
                
                // Inserir meta
                $stmt = $this->pdo->prepare("
                    INSERT INTO metas (nome, descricao, programa_id, meta_numerica, valor_atual, prazo, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, NOW()) RETURNING id
                ");
                $meta_id = $stmt->execute([
                    $input['descricao'],
                    $input['descricao'],
                    $programa_id,
                    $input['meta_numerica'] ?? 0,
                    $input['valor_atual'] ?? 0,
                    $input['prazo'] ?? null
                ]) ? $this->pdo->lastInsertId() : null;
                
                return $this->sendSuccess([
                    'message' => 'Meta adicionada com sucesso',
                    'id' => $meta_id
                ]);
                
            } else {
                // Listar metas
                $stmt = $this->pdo->query("
                    SELECT m.*, p.nome as programa_nome 
                    FROM metas m 
                    LEFT JOIN programas p ON m.programa_id = p.id 
                    ORDER BY m.id DESC
                ");
                $metas = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
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
                
                $stmt = $this->pdo->prepare("
                    INSERT INTO followups (programa, acao, status, prazo, observacoes, created_at) 
                    VALUES (?, ?, ?, ?, ?, NOW()) RETURNING id
                ");
                
                $success = $stmt->execute([
                    $input['programa'],
                    $input['acao'],
                    $input['status'],
                    $input['prazo'],
                    $input['observacoes'] ?? ''
                ]);
                
                return $this->sendSuccess([
                    'message' => 'Follow-up adicionado com sucesso',
                    'id' => $this->pdo->lastInsertId()
                ]);
                
            } else {
                $stmt = $this->pdo->query("SELECT * FROM followups ORDER BY created_at DESC");
                $followups = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
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
                
                $stmt = $this->pdo->prepare("
                    INSERT INTO cronograma (programa, descricao, valor, data_prevista, observacoes, created_at) 
                    VALUES (?, ?, ?, ?, ?, NOW()) RETURNING id
                ");
                
                $success = $stmt->execute([
                    $input['programa'],
                    $input['descricao'],
                    $input['valor'],
                    $input['data_prevista'],
                    $input['observacoes'] ?? ''
                ]);
                
                return $this->sendSuccess([
                    'message' => 'Item do cronograma adicionado com sucesso',
                    'id' => $this->pdo->lastInsertId()
                ]);
                
            } else {
                $stmt = $this->pdo->query("SELECT * FROM cronograma ORDER BY data_prevista ASC");
                $cronograma = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
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
                $programas = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                return $this->sendSuccess(['data' => $programas]);
            }
            
            return $this->sendError('Método não suportado para programas');
            
        } catch (PDOException $e) {
            return $this->sendError('Erro nos programas: ' . $e->getMessage());
        }
    }
    
    private function handleInventario($method) {
        try {
            // Criar tabela de inventário se não existir
            $this->pdo->exec("
                CREATE TABLE IF NOT EXISTS inventario (
                    id SERIAL PRIMARY KEY,
                    programa VARCHAR(255) NOT NULL,
                    equipamento VARCHAR(255) NOT NULL,
                    quantidade INTEGER NOT NULL,
                    valor_unitario DECIMAL(15,2) NOT NULL,
                    observacoes TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            ");
            
            if ($method === 'POST') {
                $input = $this->getJsonInput();
                
                $stmt = $this->pdo->prepare("
                    INSERT INTO inventario (programa, equipamento, quantidade, valor_unitario, observacoes) 
                    VALUES (?, ?, ?, ?, ?)
                ");
                
                $success = $stmt->execute([
                    $input['programa'],
                    $input['equipamento'],
                    $input['quantidade'],
                    $input['valor_unitario'],
                    $input['observacoes'] ?? ''
                ]);
                
                return $this->sendSuccess([
                    'message' => 'Item do inventário adicionado com sucesso',
                    'id' => $this->pdo->lastInsertId()
                ]);
                
            } else {
                $stmt = $this->pdo->query("SELECT * FROM inventario ORDER BY created_at DESC");
                $inventario = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                return $this->sendSuccess(['data' => $inventario]);
            }
            
        } catch (PDOException $e) {
            return $this->sendError('Erro no inventário: ' . $e->getMessage());
        }
    }
    
    private function getDashboardData() {
        try {
            $dashboard = [];
            
            // Contar metas
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM metas");
            $dashboard['total_metas'] = $stmt->fetchColumn();
            
            // Contar follow-ups
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM followups");
            $dashboard['total_followups'] = $stmt->fetchColumn();
            
            // Contar programas
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM programas");
            $dashboard['total_programas'] = $stmt->fetchColumn();
            
            // Taxa de execução (assumindo que existe campo meta_numerica e valor_atual)
            $stmt = $this->pdo->query("
                SELECT AVG(
                    CASE 
                        WHEN meta_numerica > 0 AND valor_atual IS NOT NULL 
                        THEN (valor_atual::float / meta_numerica::float * 100) 
                        ELSE 0 
                    END
                ) as taxa 
                FROM metas 
                WHERE meta_numerica IS NOT NULL AND meta_numerica > 0
            ");
            $taxa = $stmt->fetchColumn();
            $dashboard['taxa_execucao'] = round($taxa ?? 0, 1);
            
            return $this->sendSuccess(['data' => $dashboard]);
            
        } catch (PDOException $e) {
            return $this->sendError('Erro no dashboard: ' . $e->getMessage());
        }
    }
    
    private function getJsonInput() {
        $input = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('JSON inválido');
        }
        return $input;
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
