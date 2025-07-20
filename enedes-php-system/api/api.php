<?php
// API.php CORRIGIDA - Com fixes de persistência aplicados

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Configuração do banco - suas credenciais reais
$host = 'dpg-d1u47ber433s73ebqecg-a.oregon-postgres.render.com';
$db   = 'enedesifb';
$user = 'enedesifb_user';
$pass = 'E8kQWf5R9eAUV6XZJBeYNVcgBdmcTjUB';
$port = '5432';

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$db", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_AUTOCOMMIT => true  // 🔧 FIX 1: Força autocommit
    ]);
    
    // 🔧 FIX 2: Garantir que autocommit está habilitado
    $pdo->exec("SET autocommit = ON");
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro na conexão: ' . $e->getMessage()]);
    exit;
}

// Função para logging de operações (para debugging)
function logOperation($operation, $data = null, $success = null) {
    $log = [
        'timestamp' => date('Y-m-d H:i:s'),
        'operation' => $operation,
        'data' => $data,
        'success' => $success
    ];
    error_log(json_encode($log));
}

// Criar tabelas se não existirem
function createTables($pdo) {
    try {
        // Tabela metas
        $pdo->exec("CREATE TABLE IF NOT EXISTS metas (
            id SERIAL PRIMARY KEY,
            titulo VARCHAR(255) NOT NULL,
            descricao TEXT,
            prazo DATE,
            status VARCHAR(50) DEFAULT 'ativa',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        // Tabela actions
        $pdo->exec("CREATE TABLE IF NOT EXISTS actions (
            id SERIAL PRIMARY KEY,
            meta_id INTEGER REFERENCES metas(id) ON DELETE CASCADE,
            titulo VARCHAR(255) NOT NULL,
            descricao TEXT,
            prazo DATE,
            status VARCHAR(50) DEFAULT 'pendente',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        // Tabela followups
        $pdo->exec("CREATE TABLE IF NOT EXISTS followups (
            id SERIAL PRIMARY KEY,
            action_id INTEGER REFERENCES actions(id) ON DELETE CASCADE,
            titulo VARCHAR(255) NOT NULL,
            descricao TEXT,
            data_followup DATE,
            status VARCHAR(50) DEFAULT 'agendado',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        // Tabela tasks
        $pdo->exec("CREATE TABLE IF NOT EXISTS tasks (
            id SERIAL PRIMARY KEY,
            titulo VARCHAR(255) NOT NULL,
            descricao TEXT,
            prazo DATE,
            prioridade VARCHAR(20) DEFAULT 'media',
            status VARCHAR(50) DEFAULT 'pendente',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        // Tabela schedule
        $pdo->exec("CREATE TABLE IF NOT EXISTS schedule (
            id SERIAL PRIMARY KEY,
            titulo VARCHAR(255) NOT NULL,
            descricao TEXT,
            data_inicio TIMESTAMP,
            data_fim TIMESTAMP,
            tipo VARCHAR(50) DEFAULT 'evento',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        // Tabela inventory
        $pdo->exec("CREATE TABLE IF NOT EXISTS inventory (
            id SERIAL PRIMARY KEY,
            nome VARCHAR(255) NOT NULL,
            descricao TEXT,
            quantidade INTEGER DEFAULT 0,
            categoria VARCHAR(100),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        logOperation('CREATE_TABLES', 'all_tables', true);
        
    } catch (PDOException $e) {
        logOperation('CREATE_
