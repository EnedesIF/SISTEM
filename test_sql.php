<?php
header('Content-Type: application/json');

class SQLTester {
    private $pdo;
    private $results = [];
    
    public function __construct() {
        $this->connectDatabase();
        $this->runAllTests();
        $this->outputResults();
    }
    
    private function connectDatabase() {
        try {
            $database_url = $_ENV['DATABASE_URL'] ?? '';
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
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
            
            $this->addResult('✅ CONEXÃO', 'Conectado ao PostgreSQL com sucesso');
            
        } catch (Exception $e) {
            $this->addResult('❌ CONEXÃO', 'Erro: ' . $e->getMessage());
            exit(json_encode($this->results));
        }
    }
    
    private function runAllTests() {
        $this->testBasicQueries();
        $this->testTableStructure();
        $this->testInsertOperations();
        $this->testSelectOperations();
        $this->testUpdateOperations();
        $this->testDeleteOperations();
        $this->testComplexQueries();
        $this->testDataIntegrity();
    }
    
    private function testBasicQueries() {
        $this->addResult('🔍 TESTE', 'Iniciando testes básicos...');
        
        try {
            // Versão do PostgreSQL
            $stmt = $this->pdo->query("SELECT version()");
            $version = $stmt->fetchColumn();
            $this->addResult('✅ VERSÃO', substr($version, 0, 50) . '...');
            
            // Data/hora atual
            $stmt = $this->pdo->query("SELECT NOW()");
            $now = $stmt->fetchColumn();
            $this->addResult('✅ TIMESTAMP', $now);
            
            // Usuário atual
            $stmt = $this->pdo->query("SELECT current_user, current_database()");
            $user_info = $stmt->fetch();
            $this->addResult('✅ USUÁRIO', $user_info['current_user'] . '@' . $user_info['current_database']);
            
        } catch (PDOException $e) {
            $this->addResult('❌ BÁSICOS', 'Erro: ' . $e->getMessage());
        }
    }
    
    private function testTableStructure() {
        $this->addResult('🔍 TESTE', 'Analisando estrutura das tabelas...');
        
        try {
            // Listar todas as tabelas
            $stmt = $this->pdo->query("
                SELECT table_name, 
                       (SELECT COUNT(*) FROM information_schema.columns 
                        WHERE table_name = t.table_name AND table_schema = 'public') as column_count
                FROM information_schema.tables t
                WHERE table_schema = 'public' 
                ORDER BY table_name
            ");
            $tables = $stmt->fetchAll();
            
            foreach ($tables as $table) {
                $this->addResult('📊 TABELA', "{$table['table_name']} ({$table['column_count']} colunas)");
                
                // Detalhes das colunas
                $stmt = $this->pdo->prepare("
                    SELECT column_name, data_type, is_nullable, column_default
                    FROM information_schema.columns 
                    WHERE table_name = ? AND table_schema = 'public'
                    ORDER BY ordinal_position
                ");
                $stmt->execute([$table['table_name']]);
                $columns = $stmt->fetchAll();
                
                foreach ($columns as $col) {
                    $nullable = $col['is_nullable'] === 'YES' ? 'NULL' : 'NOT NULL';
                    $default = $col['column_default'] ? " DEFAULT {$col['column_default']}" : '';
                    $this->addResult('  └─ COLUNA', "{$col['column_name']} {$col['data_type']} {$nullable}{$default}");
                }
            }
            
        } catch (PDOException $e) {
            $this->addResult('❌ ESTRUTURA', 'Erro: ' . $e->getMessage());
        }
    }
    
    private function testInsertOperations() {
        $this->addResult('🔍 TESTE', 'Testando operações INSERT...');
        
        // Teste 1: Insert em metas
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO metas (nome, descricao) 
                VALUES (?, ?) 
                RETURNING id
            ");
            $stmt->execute(['Meta Teste SQL', 'Meta criada durante teste SQL']);
            $meta_id = $stmt->fetchColumn();
            $this->addResult('✅ INSERT METAS', "Meta criada com ID: {$meta_id}");
            
            // Guardar ID para testes posteriores
            $this->test_meta_id = $meta_id;
            
        } catch (PDOException $e) {
            $this->addResult('❌ INSERT METAS', 'Erro: ' . $e->getMessage());
        }
        
        // Teste 2: Insert em followups
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO followups (programa, acao, status, prazo, observacoes, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW()) 
                RETURNING id
            ");
            $stmt->execute([
                'Programa Teste SQL',
                'Ação de teste SQL',
                'Em Teste',
                '2025-08-01',
                'Follow-up criado durante teste SQL'
            ]);
            $followup_id = $stmt->fetchColumn();
            $this->addResult('✅ INSERT FOLLOWUPS', "Follow-up criado com ID: {$followup_id}");
            
            $this->test_followup_id = $followup_id;
            
        } catch (PDOException $e) {
            $this->addResult('❌ INSERT FOLLOWUPS', 'Erro: ' . $e->getMessage());
        }
        
        // Teste 3: Insert em cronograma
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO cronograma (programa, descricao, valor, data_prevista, observacoes, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW()) 
                RETURNING id
            ");
            $stmt->execute([
                'Programa Teste SQL',
                'Item de cronograma teste',
                1500.50,
                '2025-07-30',
                'Cronograma criado durante teste SQL'
            ]);
            $cronograma_id = $stmt->fetchColumn();
            $this->addResult('✅ INSERT CRONOGRAMA', "Cronograma criado com ID: {$cronograma_id}");
            
            $this->test_cronograma_id = $cronograma_id;
            
        } catch (PDOException $e) {
            $this->addResult('❌ INSERT CRONOGRAMA', 'Erro: ' . $e->getMessage());
        }
    }
    
    private function testSelectOperations() {
        $this->addResult('🔍 TESTE', 'Testando operações SELECT...');
        
        try {
            // Contar registros
            $tables = ['metas', 'followups', 'cronograma', 'programas'];
            foreach ($tables as $table) {
                $stmt = $this->pdo->query("SELECT COUNT(*) FROM {$table}");
                $count = $stmt->fetchColumn();
                $this->addResult('📊 COUNT', "{$table}: {$count} registros");
            }
            
            // Últimos registros de cada tabela
            foreach ($tables as $table) {
                try {
                    $stmt = $this->pdo->query("SELECT * FROM {$table} ORDER BY id DESC LIMIT 3");
                    $records = $stmt->fetchAll();
                    $this->addResult('📋 ÚLTIMOS', "{$table}: " . count($records) . " registros encontrados");
                } catch (PDOException $e) {
                    $this->addResult('⚠️ SELECT', "{$table}: estrutura diferente - " . $e->getMessage());
                }
            }
            
        } catch (PDOException $e) {
            $this->addResult('❌ SELECT', 'Erro: ' . $e->getMessage());
        }
    }
    
    private function testUpdateOperations() {
        $this->addResult('🔍 TESTE', 'Testando operações UPDATE...');
        
        if (isset($this->test_meta_id)) {
            try {
                $stmt = $this->pdo->prepare("
                    UPDATE metas 
                    SET descricao = ? 
                    WHERE id = ?
                ");
                $stmt->execute(['Meta ATUALIZADA durante teste SQL', $this->test_meta_id]);
                $affected = $stmt->rowCount();
                $this->addResult('✅ UPDATE METAS', "Linhas afetadas: {$affected}");
                
            } catch (PDOException $e) {
                $this->addResult('❌ UPDATE METAS', 'Erro: ' . $e->getMessage());
            }
        }
        
        if (isset($this->test_followup_id)) {
            try {
                $stmt = $this->pdo->prepare("
                    UPDATE followups 
                    SET status = ? 
                    WHERE id = ?
                ");
                $stmt->execute(['Atualizado via SQL', $this->test_followup_id]);
                $affected = $stmt->rowCount();
                $this->addResult('✅ UPDATE FOLLOWUPS', "Linhas afetadas: {$affected}");
                
            } catch (PDOException $e) {
                $this->addResult('❌ UPDATE FOLLOWUPS', 'Erro: ' . $e->getMessage());
            }
        }
    }
    
    private function testDeleteOperations() {
        $this->addResult('🔍 TESTE', 'Testando operações DELETE...');
        
        // Deletar registros de teste criados
        if (isset($this->test_meta_id)) {
            try {
                $stmt = $this->pdo->prepare("DELETE FROM metas WHERE id = ?");
                $stmt->execute([$this->test_meta_id]);
                $affected = $stmt->rowCount();
                $this->addResult('✅ DELETE METAS', "Linhas deletadas: {$affected}");
                
            } catch (PDOException $e) {
                $this->addResult('❌ DELETE METAS', 'Erro: ' . $e->getMessage());
            }
        }
        
        if (isset($this->test_followup_id)) {
            try {
                $stmt = $this->pdo->prepare("DELETE FROM followups WHERE id = ?");
                $stmt->execute([$this->test_followup_id]);
                $affected = $stmt->rowCount();
                $this->addResult('✅ DELETE FOLLOWUPS', "Linhas deletadas: {$affected}");
                
            } catch (PDOException $e) {
                $this->addResult('❌ DELETE FOLLOWUPS', 'Erro: ' . $e->getMessage());
            }
        }
        
        if (isset($this->test_cronograma_id)) {
            try {
                $stmt = $this->pdo->prepare("DELETE FROM cronograma WHERE id = ?");
                $stmt->execute([$this->test_cronograma_id]);
                $affected = $stmt->rowCount();
                $this->addResult('✅ DELETE CRONOGRAMA', "Linhas deletadas: {$affected}");
                
            } catch (PDOException $e) {
                $this->addResult('❌ DELETE CRONOGRAMA', 'Erro: ' . $e->getMessage());
            }
        }
    }
    
    private function testComplexQueries() {
        $this->addResult('🔍 TESTE', 'Testando consultas complexas...');
        
        try {
            // JOIN entre tabelas
            $stmt = $this->pdo->query("
                SELECT m.id, m.nome, m.descricao,
                       CASE WHEN p.nome IS NOT NULL THEN p.nome ELSE 'Sem programa' END as programa
                FROM metas m
                LEFT JOIN programas p ON (
                    CASE WHEN EXISTS(SELECT 1 FROM information_schema.columns WHERE table_name = 'metas' AND column_name = 'programa_id')
                         THEN m.programa_id = p.id
                         ELSE false
                    END
                )
                LIMIT 5
            ");
            $results = $stmt->fetchAll();
            $this->addResult('✅ JOIN', count($results) . ' registros encontrados');
            
            // Agregação
            $stmt = $this->pdo->query("
                SELECT 
                    'metas' as tabela,
                    COUNT(*) as total,
                    MIN(id) as menor_id,
                    MAX(id) as maior_id
                FROM metas
                UNION ALL
                SELECT 
                    'followups' as tabela,
                    COUNT(*) as total,
                    MIN(id) as menor_id,
                    MAX(id) as maior_id
                FROM followups
            ");
            $stats = $stmt->fetchAll();
            foreach ($stats as $stat) {
                $this->addResult('📊 STATS', "{$stat['tabela']}: {$stat['total']} registros (IDs: {$stat['menor_id']}-{$stat['maior_id']})");
            }
            
        } catch (PDOException $e) {
            $this->addResult('❌ COMPLEX', 'Erro: ' . $e->getMessage());
        }
    }
    
    private function testDataIntegrity() {
        $this->addResult('🔍 TESTE', 'Verificando integridade dos dados...');
        
        try {
            // Verificar chaves primárias
            $stmt = $this->pdo->query("
                SELECT 
                    tc.table_name,
                    kc.column_name
                FROM information_schema.table_constraints tc
                JOIN information_schema.key_column_usage kc ON tc.constraint_name = kc.constraint_name
                WHERE tc.constraint_type = 'PRIMARY KEY'
                AND tc.table_schema = 'public'
                ORDER BY tc.table_name
            ");
            $primary_keys = $stmt->fetchAll();
            
            foreach ($primary_keys as $pk) {
                $this->addResult('🔑 PRIMARY KEY', "{$pk['table_name']}.{$pk['column_name']}");
            }
            
            // Verificar índices
            $stmt = $this->pdo->query("
                SELECT 
                    schemaname,
                    tablename,
                    indexname,
                    indexdef
                FROM pg_indexes 
                WHERE schemaname = 'public'
                ORDER BY tablename, indexname
            ");
            $indexes = $stmt->fetchAll();
            
            $this->addResult('📇 ÍNDICES', count($indexes) . ' índices encontrados');
            
        } catch (PDOException $e) {
            $this->addResult('❌ INTEGRITY', 'Erro: ' . $e->getMessage());
        }
    }
    
    private function addResult($category, $message) {
        $this->results[] = [
            'timestamp' => date('H:i:s'),
            'category' => $category,
            'message' => $message
        ];
    }
    
    private function outputResults() {
        $summary = [
            'total_tests' => count($this->results),
            'successful' => count(array_filter($this->results, function($r) { return strpos($r['category'], '✅') !== false; })),
            'failed' => count(array_filter($this->results, function($r) { return strpos($r['category'], '❌') !== false; })),
            'warnings' => count(array_filter($this->results, function($r) { return strpos($r['category'], '⚠️') !== false; }))
        ];
        
        echo json_encode([
            'test_execution_time' => date('Y-m-d H:i:s'),
            'summary' => $summary,
            'results' => $this->results
        ], JSON_PRETTY_PRINT);
    }
}

// Executar todos os testes
new SQLTester();
?>
