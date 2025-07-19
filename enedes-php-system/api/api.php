<?php
// ========================================
// ENEDES API - Backend PHP para Render
// ========================================

// Headers CORS (OBRIGATÓRIO)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Content-Type: application/json; charset=UTF-8');

// Responder a requisições OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit('CORS OK');
}

// Configuração do banco Neon
$host = 'ep-gentle-unit-a5p9h5ux.us-east-2.aws.neon.tech';
$dbname = 'neondb';
$username = 'neondb_owner';
$password = 'SUBSTITUA_PELA_SUA_SENHA_NEON'; // ⚠️ COLOCAR SUA SENHA REAL

// Conectar ao banco
function conectarBanco() {
    global $host, $dbname, $username, $password;
    
    try {
        $dsn = "pgsql:host=$host;dbname=$dbname;sslmode=require";
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Erro de conexão: ' . $e->getMessage()]);
        exit();
    }
}

$method = $_SERVER['REQUEST_METHOD'];
$endpoint = $_GET['endpoint'] ?? '';

// Roteamento
switch ($endpoint) {
    
    case 'test':
        echo json_encode([
            'status' => 'success',
            'message' => 'ENEDES API funcionando!',
            'timestamp' => date('Y-m-d H:i:s'),
            'method' => $method,
            'php_version' => phpversion()
        ]);
        break;
    
    case 'goals':
        $pdo = conectarBanco();
        
        if ($method === 'GET') {
            try {
                $stmt = $pdo->query("SELECT * FROM goals ORDER BY created_at DESC");
                $goals = $stmt->fetchAll();
                echo json_encode($goals);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['error' => 'Erro ao buscar metas: ' . $e->getMessage()]);
            }
            
        } elseif ($method === 'POST') {
            try {
                $input = json_decode(file_get_contents('php://input'), true);
                
                if (!$input || !isset($input['name'])) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Nome da meta é obrigatório']);
                    break;
                }
                
                $stmt = $pdo->prepare("
                    INSERT INTO goals (name, description, category, target_date, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, NOW(), NOW())
                ");
                
                $stmt->execute([
                    $input['name'],
                    $input['description'] ?? '',
                    $input['category'] ?? 'geral',
                    $input['target_date'] ?? null
                ]);
                
                echo json_encode([
                    'success' => true,
                    'id' => $pdo->lastInsertId(),
                    'message' => 'Meta criada com sucesso!'
                ]);
                
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['error' => 'Erro ao criar meta: ' . $e->getMessage()]);
            }
            
        } elseif ($method === 'PUT') {
            try {
                $input = json_decode(file_get_contents('php://input'), true);
                $id = $_GET['id'] ?? null;
                
                if (!$id || !$input) {
                    http_response_code(400);
                    echo json_encode(['error' => 'ID e dados são obrigatórios']);
                    break;
                }
                
                $stmt = $pdo->prepare("
                    UPDATE goals 
                    SET name = ?, description = ?, category = ?, target_date = ?, updated_at = NOW()
                    WHERE id = ?
                ");
                
                $stmt->execute([
                    $input['name'],
                    $input['description'] ?? '',
                    $input['category'] ?? 'geral',
                    $input['target_date'] ?? null,
                    $id
                ]);
                
                echo json_encode(['success' => true, 'message' => 'Meta atualizada!']);
                
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['error' => 'Erro ao atualizar meta: ' . $e->getMessage()]);
            }
            
        } elseif ($method === 'DELETE') {
            try {
                $id = $_GET['id'] ?? null;
                
                if (!$id) {
                    http_response_code(400);
                    echo json_encode(['error' => 'ID é obrigatório']);
                    break;
                }
                
                $stmt = $pdo->prepare("DELETE FROM goals WHERE id = ?");
                $stmt->execute([$id]);
                
                echo json_encode(['success' => true, 'message' => 'Meta deletada!']);
                
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['error' => 'Erro ao deletar meta: ' . $e->getMessage()]);
            }
            
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Método não permitido']);
        }
        break;
    
    case 'actions':
        $pdo = conectarBanco();
        
        if ($method === 'GET') {
            try {
                $goalId = $_GET['goal_id'] ?? null;
                
                if ($goalId) {
                    $stmt = $pdo->prepare("SELECT * FROM actions WHERE goal_id = ? ORDER BY created_at DESC");
                    $stmt->execute([$goalId]);
                } else {
                    $stmt = $pdo->query("SELECT * FROM actions ORDER BY created_at DESC");
                }
                
                $actions = $stmt->fetchAll();
                echo json_encode($actions);
                
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['error' => 'Erro ao buscar ações: ' . $e->getMessage()]);
            }
            
        } elseif ($method === 'POST') {
            try {
                $input = json_decode(file_get_contents('php://input'), true);
                
                if (!$input || !isset($input['goal_id']) || !isset($input['description'])) {
                    http_response_code(400);
                    echo json_encode(['error' => 'goal_id e description são obrigatórios']);
                    break;
                }
                
                $stmt = $pdo->prepare("
                    INSERT INTO actions (goal_id, description, status, created_at, updated_at) 
                    VALUES (?, ?, ?, NOW(), NOW())
                ");
                
                $stmt->execute([
                    $input['goal_id'],
                    $input['description'],
                    $input['status'] ?? 'pending'
                ]);
                
                echo json_encode([
                    'success' => true,
                    'id' => $pdo->lastInsertId(),
                    'message' => 'Ação criada com sucesso!'
                ]);
                
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['error' => 'Erro ao criar ação: ' . $e->getMessage()]);
            }
            
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Método não permitido para actions']);
        }
        break;
    
    case 'cronograma':
        if ($method === 'POST') {
            try {
                $input = json_decode(file_get_contents('php://input'), true);
                echo json_encode([
                    'success' => true,
                    'message' => 'Cronograma processado com sucesso'
                ]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['error' => 'Erro no cronograma: ' . $e->getMessage()]);
            }
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Método não permitido']);
        }
        break;
    
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint não encontrado: ' . $endpoint]);
}
?>
