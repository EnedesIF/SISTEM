<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema ENEDES - Gestão Completa com Follow-up</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/lucide@latest/dist/umd/lucide.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .modal-backdrop {
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
        }
        .fade-in {
            animation: fadeIn 0.3s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            transform: translateX(400px);
            transition: transform 0.3s ease;
        }
        .toast.show {
            transform: translateX(0);
        }
        .followup-card {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-left: 4px solid #3b82f6;
        }
        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
        }
        .status-green { background-color: #10b981; }
        .status-blue { background-color: #3b82f6; }
        .status-gray { background-color: #6b7280; }
        .status-red { background-color: #ef4444; }
        .status-yellow { background-color: #f59e0b; }
        
        .farol-verde { background-color: #10b981; }
        .farol-amarelo { background-color: #f59e0b; }
        .farol-vermelho { background-color: #ef4444; }
        
        .dashboard-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
        }
        
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Login Modal -->
    <div id="loginModal" class="fixed inset-0 modal-backdrop flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4">
            <div class="text-center mb-6">
                <h1 class="text-3xl font-bold gradient-bg bg-clip-text text-transparent">ENEDES</h1>
                <p class="text-gray-600 mt-2">Gestão Completa de Projetos</p>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Usuário:</label>
                    <select id="userSelect" class="w-full p-3 border border-gray-300 rounded-lg">
                        <option value="">Escolha um usuário...</option>
                        <option value="coord_geral">Coordenação Geral</option>
                        <option value="coord_projeto_area1">Coordenador de Projeto - Área 1</option>
                        <option value="coord_projeto_area2">Coordenador de Projeto - Área 2</option>
                        <option value="ifb_mais_empreendedor">IFB Mais Empreendedor</option>
                        <option value="rota_empreendedora">Rota Empreendedora</option>
                        <option value="lab_varejo">Lab Varejo</option>
                        <option value="lab_consumer">Lab Consumer</option>
                        <option value="estudio">Estúdio</option>
                        <option value="ifb_digital">IFB Digital</option>
                        <option value="sala_interativa">Sala Interativa</option>
                        <option value="agencia_marketing">Agência de Marketing</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Senha:</label>
                    <input type="password" id="passwordInput" class="w-full p-3 border border-gray-300 rounded-lg" placeholder="Digite: 123456">
                </div>
                <button onclick="login()" class="w-full gradient-bg text-white py-3 rounded-lg font-medium hover:opacity-90 transition-opacity">
                    ENTRAR
                </button>
                <div class="mt-2 text-xs text-gray-500 text-center">
                    Senha para todos os usuários: <strong>123456</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Main App -->
    <div id="mainApp" class="hidden">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center space-x-4">
                        <h1 class="text-2xl font-bold text-teal-600">ENEDES</h1>
                        <span class="text-gray-500">Gestão Completa de Projetos</span>
                    </div>
                    <div class="flex items-center space-x-4">
                        <button onclick="showNotifications()" class="relative p-2 text-gray-600 hover:text-gray-900">
                            <i data-lucide="bell" class="w-5 h-5"></i>
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">3</span>
                        </button>
                        <div class="flex items-center space-x-2">
                            <div class="text-right">
                                <div id="userName" class="text-sm font-medium text-gray-900"></div>
                                <div id="userRole" class="text-xs text-gray-500"></div>
                            </div>
                            <button onclick="logout()" class="p-2 text-gray-600 hover:text-gray-900">
                                <i data-lucide="log-out" class="w-5 h-5"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Navigation Tabs -->
        <nav class="bg-white border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex space-x-8">
                    <button onclick="showTab('dashboard')" class="py-4 px-1 border-b-2 border-teal-500 text-teal-600 font-medium text-sm" id="tab-dashboard">
                        <i data-lucide="home" class="w-4 h-4 inline mr-2"></i>
                        Dashboard
                    </button>
                    <button onclick="showTab('metas')" class="py-4 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium text-sm" id="tab-metas">
                        <i data-lucide="target" class="w-4 h-4 inline mr-2"></i>
                        Metas
                        <span class="ml-1 bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded-full" id="metas-count">0</span>
                    </button>
                    <button onclick="showTab('programas')" class="py-4 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium text-sm" id="tab-programas">
                        <i data-lucide="layers" class="w-4 h-4 inline mr-2"></i>
                        Programas
                        <span class="ml-1 bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full" id="programas-count">8</span>
                    </button>
                    <div id="meuProgramaTab" class="hidden">
                        <button onclick="showTab('meu-programa')" class="py-4 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium text-sm" id="tab-meu-programa">
                            <i data-lucide="briefcase" class="w-4 h-4 inline mr-2"></i>
                            <span id="meuProgramaTabTitle">Meu Programa</span>
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Content Area -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Dashboard Tab -->
            <div id="dashboard-content" class="tab-content">
                <!-- Quick Actions -->
                <div class="mb-8">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold text-gray-900">Visão Geral</h2>
                        <div class="flex space-x-2">
                            <button onclick="showMetaForm()" class="text-purple-600 hover:text-purple-800 text-sm font-medium">
                                <i data-lucide="target" class="w-4 h-4 inline mr-1"></i>
                                Nova Meta
                            </button>
                            <button onclick="showActionForm()" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                <i data-lucide="plus" class="w-4 h-4 inline mr-1"></i>
                                Nova Ação
                            </button>
                            <button onclick="showFollowUpForm()" class="text-green-600 hover:text-green-800 text-sm font-medium">
                                <i data-lucide="send" class="w-4 h-4 inline mr-1"></i>
                                Follow-up
                            </button>
                            <button onclick="exportData()" class="text-orange-600 hover:text-orange-800 text-sm font-medium">
                                <i data-lucide="download" class="w-4 h-4 inline mr-1"></i>
                                Exportar
                            </button>
                            <button onclick="adicionarDadosExemplo(); updateAllData(); showToast('Dados de exemplo adicionados! Dashboard atualizado automaticamente.', 'success');" class="text-gray-600 hover:text-gray-800 text-sm font-medium">
                                <i data-lucide="database" class="w-4 h-4 inline mr-1"></i>
                                Dados Exemplo
                            </button>
                            <button onclick="limparTodosDados(); updateAllData(); showToast('Todos os dados foram limpos! Dashboard zerado.', 'info');" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                <i data-lucide="trash-2" class="w-4 h-4 inline mr-1"></i>
                                Limpar Dados
                            </button>
                        </div>
                    </div>

                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <div class="dashboard-card">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Total de Programas</p>
                                    <p id="totalProgramas" class="text-2xl font-bold text-gray-900">8</p>
                                </div>
                                <div class="bg-blue-100 p-3 rounded-lg">
                                    <i data-lucide="layers" class="w-6 h-6 text-blue-600"></i>
                                </div>
                            </div>
                        </div>

                        <div class="dashboard-card">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Ações Ativas</p>
                                    <p id="acoesAtivas" class="text-2xl font-bold text-gray-900">0</p>
                                </div>
                                <div class="bg-green-100 p-3 rounded-lg">
                                    <i data-lucide="activity" class="w-6 h-6 text-green-600"></i>
                                </div>
                            </div>
                        </div>

                        <div class="dashboard-card">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Follow-ups Ativos</p>
                                    <p id="followupsAtivos" class="text-2xl font-bold text-gray-900">0</p>
                                </div>
                                <div class="bg-purple-100 p-3 rounded-lg">
                                    <i data-lucide="send" class="w-6 h-6 text-purple-600"></i>
                                </div>
                            </div>
                        </div>

                        <div class="dashboard-card">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Tarefas Pendentes</p>
                                    <p id="tarefasPendentes" class="text-2xl font-bold text-gray-900">0</p>
                                </div>
                                <div class="bg-orange-100 p-3 rounded-lg">
                                    <i data-lucide="clock" class="w-6 h-6 text-orange-600"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Section -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                        <div class="dashboard-card">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Status das Ações por Programa</h3>
                            <div class="chart-container">
                                <canvas id="acoesPorProgramaChart"></canvas>
                            </div>
                        </div>

                        <div class="dashboard-card">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Follow-ups por Status</h3>
                            <div class="chart-container">
                                <canvas id="followupStatusChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Farol de Acompanhamento -->
                    <div class="dashboard-card mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Farol de Acompanhamento por Programa</h3>
                        <div id="farolAcompanhamento" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- Faróis serão inseridos aqui dinamicamente -->
                        </div>
                    </div>
                </div>

                <!-- Quick Actions Cards -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Ações Rápidas</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <button onclick="showMetaForm()" class="p-4 bg-purple-50 border-2 border-purple-200 rounded-lg hover:bg-purple-100 transition-colors text-left">
                            <i data-lucide="target" class="w-6 h-6 text-purple-600 mb-2"></i>
                            <h4 class="font-medium text-purple-900">Nova Meta</h4>
                            <p class="text-sm text-purple-700">Cadastrar nova meta estratégica</p>
                        </button>

                        <button onclick="showActionForm()" class="p-4 bg-blue-50 border-2 border-blue-200 rounded-lg hover:bg-blue-100 transition-colors text-left">
                            <i data-lucide="plus" class="w-6 h-6 text-blue-600 mb-2"></i>
                            <h4 class="font-medium text-blue-900">Nova Ação</h4>
                            <p class="text-sm text-blue-700">Adicionar nova ação ao projeto</p>
                        </button>

                        <button onclick="showFollowUpForm()" class="p-4 bg-green-50 border-2 border-green-200 rounded-lg hover:bg-green-100 transition-colors text-left">
                            <i data-lucide="send" class="w-6 h-6 text-green-600 mb-2"></i>
                            <h4 class="font-medium text-green-900">Follow-up</h4>
                            <p class="text-sm text-green-700">Enviar follow-up para colaboradores</p>
                        </button>

                        <button onclick="exportData()" class="p-4 bg-orange-50 border-2 border-orange-200 rounded-lg hover:bg-orange-100 transition-colors text-left">
                            <i data-lucide="download" class="w-6 h-6 text-orange-600 mb-2"></i>
                            <h4 class="font-medium text-orange-900">Exportar</h4>
                            <p class="text-sm text-orange-700">Exportar dados do sistema</p>
                        </button>
                    </div>
                </div>

                <!-- Dashboard de Follow-ups -->
                <div class="mb-8">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Dashboard de Follow-ups</h3>
                        <div class="flex space-x-2">
                            <button onclick="filterFollowUps('all')" class="px-3 py-1 text-sm rounded-full bg-blue-500 text-white" id="filter-all">
                                Todos
                                <span class="ml-1 bg-blue-200 text-blue-700 text-xs px-2 py-1 rounded-full" id="count-all">0</span>
                            </button>
                            <button onclick="filterFollowUps('pending')" class="px-3 py-1 text-sm rounded-full bg-gray-100 text-gray-800 hover:bg-gray-200" id="filter-pending">
                                Pendentes
                                <span class="ml-1 bg-yellow-200 text-yellow-700 text-xs px-2 py-1 rounded-full" id="count-pending">0</span>
                            </button>
                            <button onclick="filterFollowUps('in_progress')" class="px-3 py-1 text-sm rounded-full bg-gray-100 text-gray-800 hover:bg-gray-200" id="filter-in_progress">
                                Em Andamento
                                <span class="ml-1 bg-blue-200 text-blue-700 text-xs px-2 py-1 rounded-full" id="count-in_progress">0</span>
                            </button>
                            <button onclick="filterFollowUps('completed')" class="px-3 py-1 text-sm rounded-full bg-gray-100 text-gray-800 hover:bg-gray-200" id="filter-completed">
                                Concluídos
                                <span class="ml-1 bg-green-200 text-green-700 text-xs px-2 py-1 rounded-full" id="count-completed">0</span>
                            </button>
                        </div>
                    </div>
                    <div id="followUpsList" class="space-y-4">
                        <div class="flex items-center justify-center py-12 text-gray-500">
                            <div class="text-center">
                                <i data-lucide="send" class="w-12 h-12 mx-auto mb-4 text-gray-300"></i>
                                <p>Nenhum follow-up encontrado</p>
                                <p class="text-sm">Crie um follow-up para começar</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Metas Tab -->
            <div id="metas-content" class="tab-content hidden">
                <div class="bg-white border border-gray-200 rounded-lg">
                    <div class="flex border-b">
                        <button onclick="showMetasSubTab('estrategicas')" class="px-4 py-3 text-sm font-medium border-b-2 border-purple-500 text-purple-600" id="metas-tab-estrategicas">
                            <i data-lucide="target" class="w-4 h-4 inline mr-2"></i>
                            Metas Estratégicas
                        </button>
                        <button onclick="showMetasSubTab('cronograma')" class="px-4 py-3 text-sm font-medium text-gray-600 hover:text-gray-900" id="metas-tab-cronograma">
                            <i data-lucide="calendar" class="w-4 h-4 inline mr-2"></i>
                            Cronograma
                        </button>
                    </div>
                    
                    <!-- Metas Estratégicas -->
                    <div id="metas-estrategicas-content" class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="font-semibold text-gray-900">Metas Estratégicas</h4>
                            <button onclick="showMetaForm()" class="text-purple-600 hover:text-purple-800 text-sm font-medium">
                                <i data-lucide="plus" class="w-4 h-4 inline mr-1"></i>
                                Cadastrar Nova Meta
                            </button>
                        </div>
                        <div id="metasList" class="space-y-3">
                            <p class="text-gray-500 text-sm">Nenhuma meta cadastrada ainda.</p>
                        </div>
                        
                        <div class="mt-8">
                            <h5 class="font-medium text-gray-900 mb-4">Follow-ups de Metas</h5>
                            <div id="metasFollowUps" class="space-y-3">
                                <p class="text-gray-500 text-sm">Nenhum follow-up registrado ainda.</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Cronograma -->
                    <div id="metas-cronograma-content" class="p-6 hidden">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="font-semibold text-gray-900">Cronograma de Execução</h4>
                            <button onclick="showCronogramaForm()" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                <i data-lucide="plus" class="w-4 h-4 inline mr-1"></i>
                                Nova Etapa
                            </button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full table-auto">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Etapa/Programa</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Início</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prazo Final</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rubrica (R$)</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Executado (R$)</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Saldo (R$)</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status (%)</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="cronogramaTable" class="bg-white divide-y divide-gray-200">
                                    <tr>
                                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">Nenhuma etapa cadastrada ainda.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Programas Tab -->
            <div id="programas-content" class="tab-content hidden">
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">Programas do Projeto</h2>
                    <p class="text-gray-600">Gerencie todos os programas e suas ações, follow-ups e inventários</p>
                </div>
                <div id="programsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <!-- Programs will be loaded here -->
                </div>
            </div>

            <!-- Meu Programa Tab -->
            <div id="meu-programa-content" class="tab-content hidden">
                <div class="mb-6">
                    <h2 id="meuProgramaTitle" class="text-xl font-semibold text-gray-900 mb-2">Meu Programa</h2>
                    <p class="text-gray-600">Gerencie as ações, follow-ups e inventário do seu programa</p>
                </div>
                <div id="meuProgramaContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </main>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="toast bg-white border border-gray-200 rounded-lg shadow-lg p-4 flex items-center space-x-3">
        <div id="toastIcon"></div>
        <span id="toastMessage" class="text-gray-900"></span>
        <button onclick="hideToast()" class="text-gray-400 hover:text-gray-600">
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>
    </div>

    <!-- Modal Container -->
    <div id="modalContainer"></div>

    <script>
        // Helper function to translate status
        function translateStatus(status) {
            const translations = {
                'pending': 'Pendente',
                'in_progress': 'Em Andamento', 
                'completed': 'Concluída'
            };
            return translations[status] || status;
        }

        // Helper function to get status color class
        function getStatusColorClass(status) {
            const colors = {
                'pending': 'bg-gray-100 text-gray-800',
                'in_progress': 'bg-blue-100 text-blue-800',
                'completed': 'bg-green-100 text-green-800'
            };
            return colors[status] || 'bg-gray-100 text-gray-800';
        }
        function createLucideIcons() {
            if (typeof lucide !== 'undefined' && lucide.createIcons) {
                lucide.createIcons();
            }
        }

        // Global Variables
        let currentUser = null;
        let currentTab = 'dashboard';
        let currentFilter = 'all';
        
        // Charts
        let acoesPorProgramaChart = null;
        let followupStatusChart = null;

        // Data Arrays
        let actions = [];
        let followUps = [];
        let tasks = [];
        let metas = [];
        let cronograma = [];
        let inventario = [];
        let activityLog = [];

        // User definitions
        const users = {
            coord_geral: { name: "Coordenação Geral", type: "coord_geral", program: null },
            coord_projeto_area1: { name: "Coordenador de Projeto - Área 1", type: "coord_projeto", program: null },
            coord_projeto_area2: { name: "Coordenador de Projeto - Área 2", type: "coord_projeto", program: null },
            ifb_mais_empreendedor: { name: "IFB Mais Empreendedor", type: "coord_programa", program: "ifb_mais_empreendedor" },
            rota_empreendedora: { name: "Rota Empreendedora", type: "coord_programa", program: "rota_empreendedora" },
            lab_varejo: { name: "Lab Varejo", type: "coord_programa", program: "lab_varejo" },
            lab_consumer: { name: "Lab Consumer", type: "coord_programa", program: "lab_consumer" },
            estudio: { name: "Estúdio", type: "coord_programa", program: "estudio" },
            ifb_digital: { name: "IFB Digital", type: "coord_programa", program: "ifb_digital" },
            sala_interativa: { name: "Sala Interativa", type: "coord_programa", program: "sala_interativa" },
            agencia_marketing: { name: "Agência de Marketing", type: "coord_programa", program: "agencia_marketing" }
        };

        // Programs definition
        const programs = [
            { id: 1, name: "IFB Mais Empreendedor", slug: "ifb_mais_empreendedor", description: "Programa de fomento ao empreendedorismo", progress: 75, color: "blue", icon: "rocket" },
            { id: 2, name: "Rota Empreendedora", slug: "rota_empreendedora", description: "Capacitação em empreendedorismo", progress: 60, color: "green", icon: "map" },
            { id: 3, name: "Lab Varejo", slug: "lab_varejo", description: "Laboratório de inovação no varejo", progress: 80, color: "purple", icon: "shopping-cart" },
            { id: 4, name: "Lab Consumer", slug: "lab_consumer", description: "Pesquisa e desenvolvimento consumer", progress: 45, color: "pink", icon: "users" },
            { id: 5, name: "Estúdio", slug: "estudio", description: "Produção de conteúdo audiovisual", progress: 90, color: "red", icon: "video" },
            { id: 6, name: "IFB Digital", slug: "ifb_digital", description: "Transformação digital", progress: 55, color: "indigo", icon: "smartphone" },
            { id: 7, name: "Sala Interativa", slug: "sala_interativa", description: "Espaço de aprendizagem interativa", progress: 70, color: "yellow", icon: "monitor" },
            { id: 8, name: "Agência de Marketing", slug: "agencia_marketing", description: "Estratégias de marketing digital", progress: 85, color: "teal", icon: "megaphone" }
        ];

        // Collaborators definition
        const collaborators = [
            { id: 1, name: "Ana Silva", program: "Lab Consumer", email: "ana.silva@ifb.edu.br" },
            { id: 2, name: "Carlos Santos", program: "Lab Varejo", email: "carlos.santos@ifb.edu.br" },
            { id: 3, name: "Maria Oliveira", program: "Rota Empreendedora", email: "maria.oliveira@ifb.edu.br" },
            { id: 4, name: "João Costa", program: "IFB Mais Empreendedor", email: "joao.costa@ifb.edu.br" },
            { id: 5, name: "Patricia Lima", program: "Estúdio", email: "patricia.lima@ifb.edu.br" },
            { id: 6, name: "Roberto Ferreira", program: "IFB Digital", email: "roberto.ferreira@ifb.edu.br" },
            { id: 7, name: "Lucia Mendes", program: "Sala Interativa", email: "lucia.mendes@ifb.edu.br" },
            { id: 8, name: "Fernando Rocha", program: "Agência de Marketing", email: "fernando.rocha@ifb.edu.br" }
        ];

        // Login function
        function login() {
            console.log("Função login chamada"); // Debug
            const userType = document.getElementById("userSelect").value;
            const password = document.getElementById("passwordInput").value;

            console.log("Usuário selecionado:", userType); // Debug
            console.log("Senha digitada:", password); // Debug

            if (!userType) {
                alert("Por favor, selecione um usuário!");
                return;
            }

            if (password !== "123456") {
                alert("Senha incorreta! Use: 123456");
                return;
            }

            console.log("Login válido, processando..."); // Debug

            currentUser = users[userType];
            document.getElementById("loginModal").classList.add("hidden");
            document.getElementById("mainApp").classList.remove("hidden");
            
            document.getElementById("userName").textContent = currentUser.name;
            document.getElementById("userRole").textContent = currentUser.type === "coord_geral" ? "Coordenação Geral" : 
                                                            currentUser.type === "coord_projeto" ? "Coordenador de Projeto" : 
                                                            "Coordenador de Programa";

            // Show "Meu Programa" tab for program coordinators
            if (currentUser.type === "coord_programa") {
                document.getElementById("meuProgramaTab").classList.remove("hidden");
                const program = programs.find(p => p.slug === currentUser.program);
                if (program) {
                    document.getElementById("meuProgramaTabTitle").textContent = program.name;
                }
            }

            console.log("Carregando dados..."); // Debug
            loadData();
            
            // Se não há dados, adicionar dados de exemplo
            if (actions.length === 0 && metas.length === 0) {
                console.log("Nenhum dado encontrado, adicionando dados de exemplo...");
                adicionarDadosExemplo();
            }
            
            updateAllData();
            initializeCharts();
            
            // Forçar uma segunda atualização após um pequeno delay para garantir que tudo carregou
            setTimeout(() => {
                updateAllData();
                console.log("Atualização adicional completa"); // Debug
            }, 500);
            
            logActivity(`Login realizado por ${currentUser.name}`, "info", "log-in");
            console.log("Login completo!"); // Debug
        }

        // Data management
        function saveData() {
            const data = {
                actions,
                followUps,
                tasks,
                metas,
                cronograma,
                inventario,
                activityLog
            };
            localStorage.setItem('enedes_data', JSON.stringify(data));
        }

        function loadData() {
            const savedData = localStorage.getItem('enedes_data');
            if (savedData) {
                const data = JSON.parse(savedData);
                actions = data.actions || [];
                followUps = data.followUps || [];
                tasks = data.tasks || [];
                metas = data.metas || [];
                cronograma = data.cronograma || [];
                inventario = data.inventario || [];
                activityLog = data.activityLog || [];
            }
        }

        function logActivity(message, type = "info", icon = "info") {
            activityLog.unshift({
                id: Date.now(),
                message,
                type,
                icon,
                user: currentUser?.name || "Sistema",
                timestamp: new Date().toISOString()
            });
            
            if (activityLog.length > 100) {
                activityLog = activityLog.slice(0, 100);
            }
            
            saveData();
        }

        // Tab management
        function showTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            
            // Remove active class from all tabs
            document.querySelectorAll('[id^="tab-"]').forEach(tab => {
                tab.classList.remove('border-teal-500', 'text-teal-600');
                tab.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700');
            });
            
            // Show selected tab content
            document.getElementById(`${tabName}-content`).classList.remove('hidden');
            
            // Add active class to selected tab
            const activeTab = document.getElementById(`tab-${tabName}`);
            activeTab.classList.add('border-teal-500', 'text-teal-600');
            activeTab.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700');
            
            currentTab = tabName;
            
            // Load specific content based on tab
            if (tabName === 'programas') {
                renderPrograms();
            } else if (tabName === 'metas') {
                loadMetas();
            } else if (tabName === 'meu-programa') {
                loadMeuPrograma();
            } else if (tabName === 'dashboard') {
                updateDashboardCharts();
                updateFarolAcompanhamento();
            }
        }

        function showMetasSubTab(tabName) {
            // Update tab buttons
            document.getElementById("metas-tab-estrategicas").classList.remove("border-purple-500", "text-purple-600");
            document.getElementById("metas-tab-estrategicas").classList.add("text-gray-600", "hover:text-gray-900");
            document.getElementById("metas-tab-cronograma").classList.remove("border-purple-500", "text-purple-600");
            document.getElementById("metas-tab-cronograma").classList.add("text-gray-600", "hover:text-gray-900");
            
            document.getElementById(`metas-tab-${tabName}`).classList.add("border-purple-500", "text-purple-600");
            document.getElementById(`metas-tab-${tabName}`).classList.remove("text-gray-600", "hover:text-gray-900");

            // Update content
            document.getElementById("metas-estrategicas-content").classList.toggle("hidden", tabName !== "estrategicas");
            document.getElementById("metas-cronograma-content").classList.toggle("hidden", tabName !== "cronograma");
            
            if (tabName === "cronograma") {
                loadCronograma();
            }
        }

        // Função para forçar atualização completa de todos os dashboards
        function forceUpdateAllDashboards() {
            console.log("Forçando atualização completa de todos os dashboards...");
            
            // Atualizar dashboard principal
            updateAllData();
            
            // Atualizar gráficos principais
            setTimeout(() => {
                updateDashboardCharts();
                updateFarolAcompanhamento();
            }, 100);
            
            // Se estiver na aba de programas, recarregar
            if (currentTab === 'programas') {
                setTimeout(renderPrograms, 200);
            }
            
            // Se estiver na aba meu programa, recarregar
            if (currentTab === 'meu-programa') {
                setTimeout(loadMeuPrograma, 200);
            }
            
            // Se houver modal de programa aberto, atualizar
            const modalContainer = document.getElementById("modalContainer");
            if (modalContainer && modalContainer.innerHTML.trim() !== "") {
                const programDashboard = document.getElementById("program-details-dashboard-content");
                if (programDashboard && !programDashboard.classList.contains('hidden')) {
                    const modalTitle = modalContainer.querySelector('h3');
                    if (modalTitle) {
                        const programName = modalTitle.textContent.trim();
                        const program = programs.find(p => p.name === programName);
                        if (program) {
                            console.log("Atualizando dashboard do programa no modal:", program.name);
                            setTimeout(() => loadProgramDashboard(program.id), 300);
                        }
                    }
                }
            }
            
            console.log("Atualização completa finalizada");
        }
        function updateAllData() {
            console.log("Atualizando todos os dados..."); // Debug
            updateStats();
            updateFollowUpsList();
            updateDashboardCharts();
            updateFarolAcompanhamento();
            
            // Atualizar contadores de filtros
            filterFollowUps(currentFilter);
            
            if (currentTab === 'metas') {
                loadMetas();
            }
            if (currentTab === 'programas') {
                renderPrograms();
            }
            if (currentTab === 'meu-programa') {
                loadMeuPrograma();
            }
            
            // CORRIGIDO: Atualizar dashboard do programa se modal estiver aberto
            const modalContainer = document.getElementById("modalContainer");
            if (modalContainer && modalContainer.innerHTML.trim() !== "") {
                const programDashboard = document.getElementById("program-details-dashboard-content");
                if (programDashboard && !programDashboard.classList.contains('hidden')) {
                    // Tentar identificar qual programa está sendo visualizado através do título do modal
                    const modalTitle = modalContainer.querySelector('h3');
                    if (modalTitle) {
                        const programName = modalTitle.textContent.trim();
                        const program = programs.find(p => p.name === programName);
                        if (program) {
                            console.log("Atualizando dashboard do programa aberto:", program.name);
                            setTimeout(() => loadProgramDashboard(program.id), 100);
                        }
                    }
                }
            }
            
            console.log("Dados atualizados com sucesso!"); // Debug
        }

        // Update statistics
        function updateStats() {
            // Dados dinâmicos baseados nos arrays reais
            const totalProgramas = programs.length;
            const acoesAtivas = actions.length;
            const followupsAtivos = followUps.length;
            const tarefasPendentes = tasks.filter(t => t.status === "pending").length;
            const metasTotal = metas.length;
            
            console.log("Atualizando estatísticas:", {
                totalProgramas,
                acoesAtivas, 
                followupsAtivos,
                tarefasPendentes,
                metasTotal
            });
            
            // Atualizar elementos do DOM
            document.getElementById("totalProgramas").textContent = totalProgramas;
            document.getElementById("acoesAtivas").textContent = acoesAtivas;
            document.getElementById("followupsAtivos").textContent = followupsAtivos;
            document.getElementById("tarefasPendentes").textContent = tarefasPendentes;
            
            // Atualizar badges das abas
            const metasCountEl = document.getElementById("metas-count");
            const programasCountEl = document.getElementById("programas-count");
            
            if (metasCountEl) metasCountEl.textContent = metasTotal;
            if (programasCountEl) programasCountEl.textContent = totalProgramas;
        }

        // Initialize Charts
        function initializeCharts() {
            try {
                // Verificar se Chart.js está disponível
                if (typeof Chart === 'undefined') {
                    console.error("Chart.js não está carregado");
                    setTimeout(initializeCharts, 1000);
                    return;
                }

                // Ações por Programa Chart
                const ctx1 = document.getElementById('acoesPorProgramaChart');
                if (!ctx1) {
                    console.error("Canvas acoesPorProgramaChart não encontrado");
                    return;
                }

                acoesPorProgramaChart = new Chart(ctx1.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: [],
                        datasets: [{
                            label: 'Pendentes',
                            data: [],
                            backgroundColor: '#f59e0b'
                        }, {
                            label: 'Em Andamento',
                            data: [],
                            backgroundColor: '#3b82f6'
                        }, {
                            label: 'Concluídas',
                            data: [],
                            backgroundColor: '#10b981'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                stacked: true
                            },
                            y: {
                                stacked: true,
                                beginAtZero: true
                            }
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            }
                        }
                    }
                });

                // Follow-ups Status Chart
                const ctx2 = document.getElementById('followupStatusChart');
                if (!ctx2) {
                    console.error("Canvas followupStatusChart não encontrado");
                    return;
                }

                followupStatusChart = new Chart(ctx2.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Pendentes', 'Em Andamento', 'Concluídos'],
                        datasets: [{
                            data: [0, 0, 0],
                            backgroundColor: ['#f59e0b', '#3b82f6', '#10b981']
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom'
                            }
                        }
                    }
                });

                console.log("Gráficos inicializados com sucesso");
                
                // Atualizar gráficos com dados atuais
                setTimeout(updateDashboardCharts, 100);
                
            } catch (error) {
                console.error("Erro ao inicializar gráficos:", error);
                setTimeout(initializeCharts, 2000);
            }
        }

        // Update Dashboard Charts
        function updateDashboardCharts() {
            if (!acoesPorProgramaChart || !followupStatusChart) {
                console.log("Gráficos não inicializados ainda, tentando novamente...");
                setTimeout(updateDashboardCharts, 500);
                return;
            }

            try {
                // Update Ações por Programa Chart
                const programLabels = programs.map(p => p.name);
                const pendingData = programs.map(p => actions.filter(a => a.programa === p.name && a.status === 'pending').length);
                const inProgressData = programs.map(p => actions.filter(a => a.programa === p.name && a.status === 'in_progress').length);
                const completedData = programs.map(p => actions.filter(a => a.programa === p.name && a.status === 'completed').length);

                acoesPorProgramaChart.data.labels = programLabels;
                acoesPorProgramaChart.data.datasets[0].data = pendingData;
                acoesPorProgramaChart.data.datasets[1].data = inProgressData;
                acoesPorProgramaChart.data.datasets[2].data = completedData;
                acoesPorProgramaChart.update('none'); // Sem animação para melhor performance

                // Update Follow-ups Status Chart
                const pendingFollowUps = followUps.filter(f => f.status === 'pending').length;
                const inProgressFollowUps = followUps.filter(f => f.status === 'in_progress').length;
                const completedFollowUps = followUps.filter(f => f.status === 'completed').length;

                followupStatusChart.data.datasets[0].data = [pendingFollowUps, inProgressFollowUps, completedFollowUps];
                followupStatusChart.update('none'); // Sem animação para melhor performance

                console.log("Gráficos atualizados com sucesso");
            } catch (error) {
                console.error("Erro ao atualizar gráficos:", error);
            }
        }

        // Update Farol de Acompanhamento
        function updateFarolAcompanhamento() {
            const container = document.getElementById('farolAcompanhamento');
            container.innerHTML = '';

            programs.forEach(program => {
                const programActions = actions.filter(a => a.programa === program.name);
                const programFollowUps = followUps.filter(f => {
                    const targetItem = f.type === "action" ? 
                        actions.find(a => a.id == f.targetId) : 
                        metas.find(m => m.id == f.targetId);
                    return targetItem && (targetItem.programa === program.name);
                });

                // Calculate status based on actions and follow-ups
                let status = 'verde'; // Default green
                const totalItems = programActions.length + programFollowUps.length;
                
                if (totalItems === 0) {
                    status = 'cinza';
                } else {
                    const pendingActions = programActions.filter(a => a.status === 'pending').length;
                    const pendingFollowUps = programFollowUps.filter(f => f.status === 'pending').length;
                    const totalPending = pendingActions + pendingFollowUps;
                    
                    const pendingPercentage = (totalPending / totalItems) * 100;
                    
                    if (pendingPercentage > 50) {
                        status = 'vermelho';
                    } else if (pendingPercentage > 25) {
                        status = 'amarelo';
                    }
                }

                const farolCard = document.createElement('div');
                farolCard.className = 'bg-white border border-gray-200 rounded-lg p-4';
                farolCard.innerHTML = `
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-medium text-gray-900 text-sm">${program.name}</h4>
                        <div class="w-4 h-4 rounded-full farol-${status === 'cinza' ? 'gray' : status}" title="Status: ${status}"></div>
                    </div>
                    <div class="text-xs text-gray-600">
                        <div>Ações: ${programActions.length}</div>
                        <div>Follow-ups: ${programFollowUps.length}</div>
                        <div>Progresso: ${program.progress}%</div>
                    </div>
                `;
                container.appendChild(farolCard);
            });
        }

        // Follow-up functions
        function showFollowUpForm(actionId = "", metaId = "", type = "") {
            let targetOptions = "";
            
            if (actionId) {
                const action = actions.find(a => a.id == actionId);
                targetOptions = `<option value="${actionId}" selected>Ação: ${action?.titulo || "Não encontrada"}</option>`;
            } else if (metaId) {
                const meta = metas.find(m => m.id == metaId);
                targetOptions = `<option value="${metaId}" selected>Meta: ${meta?.titulo || "Não encontrada"}</option>`;
            } else {
                // Show all actions and metas
                const actionOptions = actions.map(a => `<option value="${a.id}" data-type="action">Ação: ${a.titulo}</option>`).join("");
                const metaOptions = metas.map(m => `<option value="${m.id}" data-type="meta">Meta: ${m.titulo}</option>`).join("");
                targetOptions = `<option value="">Selecione uma ação ou meta...</option>${actionOptions}${metaOptions}`;
            }

            const content = `
                <div class="space-y-4">
                    ${!actionId && !metaId ? `
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Selecionar Ação ou Meta</label>
                        <select id="followUpTarget" class="w-full p-2 border border-gray-300 rounded-lg" onchange="updateFollowUpType()">
                            ${targetOptions}
                        </select>
                        <input type="hidden" id="followUpType" value="${type}">
                    </div>
                    ` : `
                    <input type="hidden" id="followUpTarget" value="${actionId || metaId}">
                    <input type="hidden" id="followUpType" value="${type}">
                    `}
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Selecionar Colaboradores</label>
                        <div class="max-h-32 overflow-y-auto border border-gray-300 rounded-lg p-2 space-y-2">
                            ${collaborators.map(collab => `
                                <label class="flex items-center space-x-2 p-2 hover:bg-gray-50 rounded">
                                    <input type="checkbox" id="collab_${collab.id}" class="collaborator-checkbox" value="${collab.id}">
                                    <div class="flex-1">
                                        <div class="text-sm font-medium text-gray-900">${collab.name}</div>
                                        <div class="text-xs text-gray-500">${collab.program} • ${collab.email}</div>
                                    </div>
                                </label>
                            `).join("")}
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mensagem do Follow-up</label>
                        <textarea id="followUpMessage" class="w-full p-2 border border-gray-300 rounded-lg" rows="4" 
                                  placeholder="Descreva o que precisa ser feito, prazos, objetivos..."></textarea>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Prioridade</label>
                            <select id="followUpPriority" class="w-full p-2 border border-gray-300 rounded-lg">
                                <option value="baixa">Baixa</option>
                                <option value="media" selected>Média</option>
                                <option value="alta">Alta</option>
                                <option value="urgente">Urgente</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Prazo</label>
                            <input type="date" id="followUpDeadline" class="w-full p-2 border border-gray-300 rounded-lg">
                        </div>
                    </div>

                    <!-- Seção de Tarefas -->
                    <div class="border-t pt-4">
                        <div class="flex justify-between items-center mb-3">
                            <label class="block text-sm font-medium text-gray-700">Tarefas do Follow-up (opcional)</label>
                            <button type="button" onclick="addTaskToFollowUp()" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                <i data-lucide="plus" class="w-4 h-4 inline mr-1"></i>
                                Adicionar Tarefa
                            </button>
                        </div>
                        <div id="followUpTasksContainer" class="space-y-3">
                            <!-- Tarefas serão adicionadas aqui dinamicamente -->
                        </div>
                    </div>
                </div>
            `;

            const footer = `
                <button onclick="closeModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancelar</button>
                <button onclick="sendFollowUp()" class="gradient-bg text-white px-4 py-2 rounded-lg hover:opacity-90">Enviar Follow-up</button>
            `;

            createModal("Novo Follow-up", content, footer);
        }

        function updateFollowUpType() {
            const select = document.getElementById("followUpTarget");
            if (!select) return;
            
            const selectedOption = select.options[select.selectedIndex];
            const type = selectedOption ? selectedOption.getAttribute("data-type") || "" : "";
            const typeField = document.getElementById("followUpType");
            if (typeField) {
                typeField.value = type;
            }
        }

        function sendFollowUp() {
            const targetId = document.getElementById("followUpTarget").value;
            const type = document.getElementById("followUpType").value;
            const message = document.getElementById("followUpMessage").value;
            const priority = document.getElementById("followUpPriority").value;
            const deadline = document.getElementById("followUpDeadline").value;

            if (!targetId) {
                alert("Por favor, selecione uma ação ou meta!");
                return;
            }

            if (!message.trim()) {
                alert("Por favor, digite uma mensagem para o follow-up!");
                return;
            }

            // Get selected collaborators
            const selectedCollaborators = [];
            document.querySelectorAll(".collaborator-checkbox:checked").forEach(checkbox => {
                const collabId = parseInt(checkbox.value);
                const collaborator = collaborators.find(c => c.id === collabId);
                if (collaborator) {
                    selectedCollaborators.push(collaborator);
                }
            });

            if (selectedCollaborators.length === 0) {
                alert("Por favor, selecione pelo menos um colaborador!");
                return;
            }

            // Create follow-up
            const followUp = {
                id: Date.now(),
                targetId: parseInt(targetId),
                type: type,
                mensagem: message,
                prioridade: priority,
                prazo: deadline,
                colaboradores: selectedCollaborators,
                status: "pending",
                createdBy: currentUser.name,
                createdAt: new Date().toISOString(),
                lastUpdate: new Date().toISOString()
            };

            followUps.push(followUp);

            // Criar tarefas associadas se existirem
            const followUpTasks = collectFollowUpTasks();
            console.log("Tarefas coletadas:", followUpTasks); // Debug

            followUpTasks.forEach(taskData => {
                const task = {
                    id: Date.now() + Math.random(), // Garantir ID único
                    followUpId: followUp.id,
                    titulo: taskData.titulo,
                    descricao: taskData.descricao,
                    responsavel: taskData.responsavel,
                    status: taskData.status || "pending",
                    prazo: taskData.prazo,
                    createdAt: new Date().toISOString(),
                    lastUpdate: new Date().toISOString()
                };
                tasks.push(task);
                console.log("Tarefa criada:", task); // Debug
            });
            
            const targetItem = type === "action" ? 
                actions.find(a => a.id == targetId) : 
                metas.find(m => m.id == targetId);
            
            logActivity(`Follow-up enviado para "${targetItem?.titulo || "Item não encontrado"}" com ${followUpTasks.length} tarefas`, "success", "send");
            
            saveData();
            forceUpdateAllDashboards(); // Usar nova função de atualização completa
            closeModal();
            showToast(`Follow-up enviado com sucesso! ${followUpTasks.length} tarefas criadas.`, "success");
        }

        // Função para adicionar tarefa ao follow-up
        function addTaskToFollowUp() {
            const container = document.getElementById("followUpTasksContainer");
            const taskIndex = container.children.length;
            
            const taskHtml = `
                <div class="border border-gray-200 rounded-lg p-3 task-item" data-task-index="${taskIndex}">
                    <div class="flex justify-between items-center mb-2">
                        <h4 class="font-medium text-gray-900">Tarefa ${taskIndex + 1}</h4>
                        <button type="button" onclick="removeTaskFromFollowUp(${taskIndex})" class="text-red-600 hover:text-red-800 text-sm">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Título *</label>
                            <input type="text" class="task-titulo w-full p-2 border border-gray-300 rounded text-sm" placeholder="Título da tarefa" required>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Responsável *</label>
                            <select class="task-responsavel w-full p-2 border border-gray-300 rounded text-sm">
                                <option value="">Selecione...</option>
                                ${collaborators.map(collab => `<option value="${collab.name}">${collab.name}</option>`).join("")}
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs text-gray-600 mb-1">Descrição</label>
                            <textarea class="task-descricao w-full p-2 border border-gray-300 rounded text-sm" rows="2" placeholder="Descreva a tarefa..."></textarea>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Prazo</label>
                            <input type="date" class="task-prazo w-full p-2 border border-gray-300 rounded text-sm">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Status</label>
                            <select class="task-status w-full p-2 border border-gray-300 rounded text-sm">
                                <option value="pending">Pendente</option>
                                <option value="in_progress">Em Andamento</option>
                                <option value="completed">Concluída</option>
                            </select>
                        </div>
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', taskHtml);
            createLucideIcons();
        }

        // Função para remover tarefa do follow-up
        function removeTaskFromFollowUp(taskIndex) {
            const taskItem = document.querySelector(`[data-task-index="${taskIndex}"]`);
            if (taskItem) {
                taskItem.remove();
                
                // Reindexar as tarefas restantes
                const remainingTasks = document.querySelectorAll('.task-item');
                remainingTasks.forEach((task, newIndex) => {
                    task.setAttribute('data-task-index', newIndex);
                    task.querySelector('h4').textContent = `Tarefa ${newIndex + 1}`;
                    const removeButton = task.querySelector('button[onclick*="removeTaskFromFollowUp"]');
                    if (removeButton) {
                        removeButton.setAttribute('onclick', `removeTaskFromFollowUp(${newIndex})`);
                    }
                });
            }
        }

        // Função para coletar dados das tarefas do follow-up
        function collectFollowUpTasks() {
            const taskItems = document.querySelectorAll('.task-item');
            const tasks = [];
            
            taskItems.forEach(item => {
                const titulo = item.querySelector('.task-titulo').value.trim();
                const descricao = item.querySelector('.task-descricao').value.trim();
                const responsavel = item.querySelector('.task-responsavel').value;
                const prazo = item.querySelector('.task-prazo').value;
                const status = item.querySelector('.task-status').value;
                
                if (titulo && responsavel) {
                    tasks.push({
                        titulo,
                        descricao,
                        responsavel,
                        prazo,
                        status
                    });
                }
            });
            
            return tasks;
        }

        function showFollowUpTasks(followUpId) {
            const followUp = followUps.find(f => f.id == followUpId);
            if (!followUp) return;

            const targetItem = followUp.type === "action" ? 
                actions.find(a => a.id == followUp.targetId) : 
                metas.find(m => m.id == followUp.targetId);

            const followUpTasks = tasks.filter(t => t.followUpId == followUpId);

            const content = `
                <div class="space-y-6">
                    <div class="bg-blue-50 rounded-lg p-4">
                        <h4 class="font-medium text-blue-900">Follow-up: ${followUp.mensagem.substring(0, 100)}${followUp.mensagem.length > 100 ? '...' : ''}</h4>
                        <p class="text-sm text-blue-700 mt-1">Colaboradores: ${followUp.colaboradores.map(c => c.name).join(', ')}</p>
                    </div>
                    
                    <div>
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="font-semibold text-gray-900">Tarefas Atreladas</h4>
                            <button onclick="showTaskForm('${followUpId}')" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                <i data-lucide="plus" class="w-4 h-4 inline mr-1"></i>
                                Nova Tarefa
                            </button>
                        </div>
                        
                        <div class="space-y-3">
                            ${followUpTasks.length === 0 ? 
                                "<p class=\"text-gray-500 text-sm\">Nenhuma tarefa atrelada ainda.</p>" :
                                followUpTasks.map(task => {
                                    const statusColor = task.status === "completed" ? "green" : 
                                                      task.status === "in_progress" ? "blue" : "gray";
                                    return `
                                        <div class="p-3 border border-gray-200 rounded-lg">
                                            <div class="flex justify-between items-start mb-2">
                                                <div class="flex items-center space-x-2">
                                                    <span class="status-indicator status-${statusColor}"></span>
                                                    <h5 class="font-medium text-gray-900">${task.titulo}</h5>
                                                </div>
                                                <div class="flex space-x-2">
                                                    <button onclick="updateTaskStatus('${task.id}')" class="text-green-600 hover:text-green-800 text-sm">
                                                        <i data-lucide="check-circle" class="w-4 h-4 inline mr-1"></i>
                                                        Status
                                                    </button>
                                                    <button onclick="deleteTask('${task.id}')" class="text-red-600 hover:text-red-800 text-sm">
                                                        <i data-lucide="trash-2" class="w-4 h-4 inline mr-1"></i>
                                                        Excluir
                                                    </button>
                                                </div>
                                            </div>
                                            <p class="text-sm text-gray-600 mb-2">${task.descricao}</p>
                                            <div class="flex justify-between items-center text-xs text-gray-500">
                                                <span>Responsável: ${task.responsavel}</span>
                                                <span>Prazo: ${task.prazo || "Não definido"}</span>
                                            </div>
                                        </div>
                                    `;
                                }).join("")
                            }
                        </div>
                    </div>
                </div>
            `;

            const footer = `
                <button onclick="closeModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Fechar</button>
            `;

            createModal("Gerenciar Tarefas", content, footer);
        }

        function showTaskForm(followUpId) {
            const followUp = followUps.find(f => f.id == followUpId);
            if (!followUp) return;

            const content = `
                <div class="space-y-4">
                    <input type="hidden" id="taskFollowUpId" value="${followUpId}">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Título da Tarefa</label>
                        <input type="text" id="taskTitle" class="w-full p-2 border border-gray-300 rounded-lg" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                        <textarea id="taskDescription" class="w-full p-2 border border-gray-300 rounded-lg" rows="3"></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Responsável</label>
                        <select id="taskResponsible" class="w-full p-2 border border-gray-300 rounded-lg">
                            ${followUp.colaboradores.map(collab => `<option value="${collab.name}">${collab.name}</option>`).join("")}
                        </select>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select id="taskStatus" class="w-full p-2 border border-gray-300 rounded-lg">
                                <option value="pending">Pendente</option>
                                <option value="in_progress">Em Andamento</option>
                                <option value="completed">Concluída</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Prazo</label>
                            <input type="date" id="taskDeadline" class="w-full p-2 border border-gray-300 rounded-lg">
                        </div>
                    </div>
                </div>
            `;

            const footer = `
                <button onclick="closeModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancelar</button>
                <button onclick="saveTask()" class="gradient-bg text-white px-4 py-2 rounded-lg hover:opacity-90">Salvar Tarefa</button>
            `;

            createModal("Nova Tarefa", content, footer);
        }

        function saveTask() {
            const followUpId = document.getElementById("taskFollowUpId").value;
            const titulo = document.getElementById("taskTitle").value;
            const descricao = document.getElementById("taskDescription").value;
            const responsavel = document.getElementById("taskResponsible").value;
            const status = document.getElementById("taskStatus").value;
            const prazo = document.getElementById("taskDeadline").value;

            if (!titulo.trim()) {
                alert("O título da tarefa é obrigatório!");
                return;
            }

            const task = {
                id: Date.now(),
                followUpId: parseInt(followUpId),
                titulo,
                descricao,
                responsavel,
                status,
                prazo,
                createdAt: new Date().toISOString(),
                lastUpdate: new Date().toISOString()
            };

            tasks.push(task);
            logActivity(`Nova tarefa "${titulo}" criada`, "success", "plus");
            
            saveData();
            updateAllData();
            closeModal();
            showToast("Tarefa criada com sucesso!", "success");
            
            // Reopen the follow-up tasks modal
            setTimeout(() => showFollowUpTasks(followUpId), 100);
        }

        // Função para atualizar status de tarefa
        function updateTaskStatus(taskId) {
            const task = tasks.find(t => t.id == taskId);
            if (!task) return;

            const content = `
                <div class="space-y-4">
                    <div class="bg-green-50 p-4 rounded-lg">
                        <h4 class="font-medium text-green-900">Tarefa: ${task.titulo}</h4>
                        <p class="text-sm text-green-700">${task.descricao}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Novo Status</label>
                        <select id="newTaskStatus" class="w-full p-2 border border-gray-300 rounded-lg">
                            <option value="pending" ${task.status === "pending" ? "selected" : ""}>Pendente</option>
                            <option value="in_progress" ${task.status === "in_progress" ? "selected" : ""}>Em Andamento</option>
                            <option value="completed" ${task.status === "completed" ? "selected" : ""}>Concluída</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Comentário (opcional)</label>
                        <textarea id="statusComment" class="w-full p-2 border border-gray-300 rounded-lg" rows="3" 
                                  placeholder="Adicione um comentário sobre a atualização..."></textarea>
                    </div>
                </div>
            `;

            const footer = `
                <button onclick="closeModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancelar</button>
                <button onclick="confirmTaskStatusUpdate('${taskId}')" class="gradient-bg text-white px-4 py-2 rounded-lg hover:opacity-90">
                    Atualizar Status
                </button>
            `;

            createModal("Atualizar Status da Tarefa", content, footer);
        }

        // Função para confirmar atualização de status
        function confirmTaskStatusUpdate(taskId) {
            const newStatus = document.getElementById("newTaskStatus").value;
            const comment = document.getElementById("statusComment").value;
            
            const taskIndex = tasks.findIndex(t => t.id == taskId);
            if (taskIndex > -1) {
                const oldStatus = tasks[taskIndex].status;
                tasks[taskIndex].status = newStatus;
                tasks[taskIndex].lastUpdate = new Date().toISOString();
                
                if (comment.trim()) {
                    if (!tasks[taskIndex].comments) tasks[taskIndex].comments = [];
                    tasks[taskIndex].comments.push({
                        text: comment,
                        author: currentUser.name,
                        timestamp: new Date().toISOString()
                    });
                }
                
                const statusText = newStatus === "completed" ? "concluída" : 
                                 newStatus === "in_progress" ? "em andamento" : "pendente";
                
                logActivity(`Status da tarefa "${tasks[taskIndex].titulo}" alterado para ${statusText}`, "info", "check-circle");
                
                saveData();
                updateAllData();
                closeModal();
                showToast("Status da tarefa atualizado!", "success");
            }
        }

        // Função para deletar tarefa
        function deleteTask(taskId) {
            if (confirm("Tem certeza que deseja excluir esta tarefa?")) {
                const taskIndex = tasks.findIndex(t => t.id == taskId);
                if (taskIndex > -1) {
                    const taskTitle = tasks[taskIndex].titulo;
                    const followUpId = tasks[taskIndex].followUpId;
                    tasks.splice(taskIndex, 1);
                    logActivity(`Tarefa "${taskTitle}" excluída`, "warning", "trash-2");
                    saveData();
                    updateAllData();
                    showToast("Tarefa excluída!", "success");
                    closeModal();
                    
                    // Reopen the follow-up tasks modal
                    setTimeout(() => showFollowUpTasks(followUpId), 100);
                }
            }
        }

        function updateFollowUpsList() {
            const container = document.getElementById("followUpsList");
            
            if (followUps.length === 0) {
                container.innerHTML = `
                    <div class="flex items-center justify-center py-12 text-gray-500">
                        <div class="text-center">
                            <i data-lucide="send" class="w-12 h-12 mx-auto mb-4 text-gray-300"></i>
                            <p>Nenhum follow-up encontrado</p>
                            <p class="text-sm">Crie um follow-up para começar</p>
                        </div>
                    </div>
                `;
                return;
            }

            let filteredFollowUps = followUps;
            if (currentFilter !== 'all') {
                filteredFollowUps = followUps.filter(f => f.status === currentFilter);
            }

            container.innerHTML = filteredFollowUps.map(followUp => {
                const targetItem = followUp.type === "action" ? 
                    actions.find(a => a.id == followUp.targetId) : 
                    metas.find(m => m.id == followUp.targetId);
                
                const statusColor = followUp.status === "completed" ? "green" : 
                                  followUp.status === "in_progress" ? "blue" : "gray";
                
                const priorityColor = followUp.prioridade === "urgente" ? "red" : 
                                    followUp.prioridade === "alta" ? "orange" : 
                                    followUp.prioridade === "media" ? "blue" : "gray";

                return `
                    <div class="followup-card p-4 rounded-lg">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-1">
                                    <span class="status-indicator status-${statusColor}"></span>
                                    <h4 class="font-medium text-gray-900">${targetItem?.titulo || "Item não encontrado"}</h4>
                                    <span class="text-xs px-2 py-1 rounded-full bg-${priorityColor}-100 text-${priorityColor}-800">${followUp.prioridade.toUpperCase()}</span>
                                </div>
                                <p class="text-sm text-gray-600 mb-2">${followUp.mensagem}</p>
                                <div class="flex items-center space-x-3 text-xs text-gray-500">
                                    <span>Por: ${followUp.createdBy}</span>
                                    <span>Colaboradores: ${followUp.colaboradores.length}</span>
                                    ${followUp.prazo ? `<span>Prazo: ${new Date(followUp.prazo).toLocaleDateString()}</span>` : ''}
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <button onclick="showFollowUpTasks('${followUp.id}')" class="text-blue-600 hover:text-blue-800 text-sm">
                                    <i data-lucide="list" class="w-4 h-4 inline mr-1"></i>
                                    Tarefas
                                </button>
                                <button onclick="updateFollowUpStatus('${followUp.id}')" class="text-green-600 hover:text-green-800 text-sm">
                                    <i data-lucide="check-circle" class="w-4 h-4 inline mr-1"></i>
                                    Status
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            }).join("");
            
            createLucideIcons();
        }

        function filterFollowUps(status) {
            currentFilter = status;
            
            // Calcular contadores dinâmicos
            const allCount = followUps.length;
            const pendingCount = followUps.filter(f => f.status === 'pending').length;
            const inProgressCount = followUps.filter(f => f.status === 'in_progress').length;
            const completedCount = followUps.filter(f => f.status === 'completed').length;
            
            // Atualizar contadores na interface
            document.getElementById("count-all").textContent = allCount;
            document.getElementById("count-pending").textContent = pendingCount;
            document.getElementById("count-in_progress").textContent = inProgressCount;
            document.getElementById("count-completed").textContent = completedCount;
            
            // Update filter buttons
            document.querySelectorAll('[id^="filter-"]').forEach(btn => {
                btn.classList.remove('bg-blue-500', 'text-white');
                btn.classList.add('bg-gray-100', 'text-gray-800');
            });
            
            document.getElementById(`filter-${status}`).classList.remove('bg-gray-100', 'text-gray-800');
            document.getElementById(`filter-${status}`).classList.add('bg-blue-500', 'text-white');
            
            updateFollowUpsList();
        }

        // Função para atualizar status de follow-up
        function updateFollowUpStatus(followUpId) {
            const followUp = followUps.find(f => f.id == followUpId);
            if (!followUp) return;

            const content = `
                <div class="space-y-4">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h4 class="font-medium text-blue-900">Follow-up</h4>
                        <p class="text-sm text-blue-700">${followUp.mensagem}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Novo Status</label>
                        <select id="newFollowUpStatus" class="w-full p-2 border border-gray-300 rounded-lg">
                            <option value="pending" ${followUp.status === "pending" ? "selected" : ""}>Pendente</option>
                            <option value="in_progress" ${followUp.status === "in_progress" ? "selected" : ""}>Em Andamento</option>
                            <option value="completed" ${followUp.status === "completed" ? "selected" : ""}>Concluído</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Observações</label>
                        <textarea id="followUpObservations" class="w-full p-2 border border-gray-300 rounded-lg" rows="3" 
                                  placeholder="Adicione observações sobre o progresso..."></textarea>
                    </div>
                </div>
            `;

            const footer = `
                <button onclick="closeModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancelar</button>
                <button onclick="confirmFollowUpStatusUpdate('${followUpId}')" class="gradient-bg text-white px-4 py-2 rounded-lg hover:opacity-90">
                    Atualizar Status
                </button>
            `;

            createModal("Atualizar Status do Follow-up", content, footer);
        }

        // Função para confirmar atualização de status do follow-up
        function confirmFollowUpStatusUpdate(followUpId) {
            const newStatus = document.getElementById("newFollowUpStatus").value;
            const observations = document.getElementById("followUpObservations").value;
            
            const followUpIndex = followUps.findIndex(f => f.id == followUpId);
            if (followUpIndex > -1) {
                followUps[followUpIndex].status = newStatus;
                followUps[followUpIndex].lastUpdate = new Date().toISOString();
                
                if (observations.trim()) {
                    if (!followUps[followUpIndex].observations) followUps[followUpIndex].observations = [];
                    followUps[followUpIndex].observations.push({
                        text: observations,
                        author: currentUser.name,
                        timestamp: new Date().toISOString()
                    });
                }
                
                const statusText = newStatus === "completed" ? "concluído" : 
                                 newStatus === "in_progress" ? "em andamento" : "pendente";
                
                logActivity(`Status do follow-up alterado para ${statusText}`, "info", "check-circle");
                
                saveData();
                updateAllData();
                closeModal();
                showToast("Status do follow-up atualizado!", "success");
            }
        }

        function renderPrograms() {
            const grid = document.getElementById("programsGrid");
            if (!grid) return;
            
            grid.innerHTML = "";
            
            let visiblePrograms = programs;
            
            if (currentUser.type === "coord_programa") {
                visiblePrograms = programs.filter(p => p.slug === currentUser.program);
            }
            
            visiblePrograms.forEach(program => {
                const card = document.createElement("div");
                card.className = "bg-gray-50 rounded-lg p-5 card-hover cursor-pointer flex flex-col";
                card.onclick = () => showProgramDetails(program.id);
                
                const programActionsCount = actions.filter(a => a.programa === program.name).length;
                const programInventoryCount = inventario.filter(i => i.programa === program.name).length;
                const programFollowUpsCount = followUps.filter(f => {
                    const targetItem = f.type === "action" ? 
                        actions.find(a => a.id == f.targetId) : 
                        metas.find(m => m.id == f.targetId);
                    return targetItem && (targetItem.programa === program.name);
                }).length;

                card.innerHTML = `
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-${program.color}-100 p-2 rounded-lg">
                            <i data-lucide="${program.icon}" class="w-6 h-6 text-${program.color}-600"></i>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-gray-500">${programActionsCount} ações</div>
                            <div class="text-xs text-gray-500">${programFollowUpsCount} follow-ups</div>
                            <div class="text-xs text-gray-500">${programInventoryCount} itens</div>
                        </div>
                    </div>
                    <div class="flex-grow">
                        <h3 class="font-semibold text-gray-900 mb-2 text-lg">${program.name}</h3>
                        <p class="text-sm text-gray-600 mb-4 h-12">${program.description}</p>
                    </div>
                    <div class="space-y-2 mt-auto">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Progresso</span>
                            <span class="font-medium text-gray-800">${program.progress}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="gradient-bg h-2.5 rounded-full" style="width: ${program.progress}%"></div>
                        </div>
                    </div>
                `;
                grid.appendChild(card);
            });
            createLucideIcons();
        }

        // CORRIGIDO: showProgramDetails com carregamento inicial correto
        function showProgramDetails(programId) {
            console.log("=== ABRINDO DETALHES DO PROGRAMA ===");
            console.log("Program ID recebido:", programId);
            
            const program = programs.find(p => p.id === programId);
            if (!program) {
                console.error("Programa não encontrado para ID:", programId);
                return;
            }

            console.log("Programa encontrado:", program);

            const programActions = actions.filter(a => a.programa === program.name);
            const programInventory = inventario.filter(i => i.programa === program.name);
            const programFollowUps = followUps.filter(f => {
                const targetItem = f.type === "action" ? 
                    actions.find(a => a.id == f.targetId) : 
                    metas.find(m => m.id == f.targetId);
                return targetItem && (targetItem.programa === program.name);
            });

            console.log("Dados do programa:", {
                programActions: programActions.length,
                programInventory: programInventory.length,
                programFollowUps: programFollowUps.length
            });

            const content = `
                <div class="space-y-6">
                    <div class="bg-${program.color}-50 rounded-lg p-6">
                        <div class="flex items-center space-x-4">
                            <div class="bg-${program.color}-100 p-3 rounded-lg">
                                <i data-lucide="${program.icon}" class="w-8 h-8 text-${program.color}-600"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold text-gray-900">${program.name}</h3>
                                <p class="text-gray-600">${program.description}</p>
                                <div class="flex items-center space-x-4 mt-2">
                                    <span class="text-sm text-gray-500">Progresso: ${program.progress}%</span>
                                    <span class="text-sm text-gray-500">Ações: ${programActions.length}</span>
                                    <span class="text-sm text-gray-500">Follow-ups: ${programFollowUps.length}</span>
                                    <span class="text-sm text-gray-500">Inventário: ${programInventory.length} itens</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tabs for Actions, Follow-ups and Inventory -->
                    <div class="border-b border-gray-200">
                        <nav class="-mb-px flex space-x-8">
                            <button onclick="showProgramDetailsSubTab('dashboard', ${program.id})" class="py-2 px-1 border-b-2 border-blue-500 text-blue-600 font-medium text-sm" id="program-details-tab-dashboard">
                                Dashboard
                            </button>
                            <button onclick="showProgramDetailsSubTab('actions', ${program.id})" class="py-2 px-1 border-b-2 border-transparent text-gray-600 hover:text-gray-900 font-medium text-sm" id="program-details-tab-actions">
                                Ações (${programActions.length})
                            </button>
                            <button onclick="showProgramDetailsSubTab('followups', ${program.id})" class="py-2 px-1 border-b-2 border-transparent text-gray-600 hover:text-gray-900 font-medium text-sm" id="program-details-tab-followups">
                                Follow-ups (${programFollowUps.length})
                            </button>
                            <button onclick="showProgramDetailsSubTab('inventory', ${program.id})" class="py-2 px-1 border-b-2 border-transparent text-gray-600 hover:text-gray-900 font-medium text-sm" id="program-details-tab-inventory">
                                Inventário (${programInventory.length})
                            </button>
                        </nav>
                    </div>
                    
                    <div id="program-details-dashboard-content" class="p-6">
                        <div class="text-center text-gray-500 py-8">
                            <div class="animate-spin inline-block w-6 h-6 border-2 border-blue-600 border-t-transparent rounded-full mb-2"></div>
                            <p>Carregando dashboard...</p>
                        </div>
                    </div>
                    
                    <!-- Actions Tab -->
                    <div id="program-details-actions-content" class="p-6 hidden">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="font-semibold text-gray-900">Ações do Programa</h4>
                            <button onclick="showActionForm('${program.name}')" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                <i data-lucide="plus" class="w-4 h-4 inline mr-1"></i>
                                Nova Ação
                            </button>
                        </div>
                        <div class="space-y-3">
                            ${programActions.length === 0 ? 
                                "<p class=\"text-gray-500 text-sm\">Nenhuma ação cadastrada ainda para este programa.</p>" :
                                programActions.map(action => `
                                    <div class="p-4 border border-gray-200 rounded-lg">
                                        <div class="flex justify-between items-start mb-2">
                                            <h5 class="font-medium text-gray-900">${action.titulo}</h5>
                                            <span class="text-xs px-2 py-1 rounded-full ${getStatusColorClass(action.status)}">${translateStatus(action.status)}</span>
                                        </div>
                                        <p class="text-sm text-gray-600 mb-2">${action.descricao}</p>
                                        <div class="flex justify-between items-center text-sm">
                                            <span class="text-gray-500">Responsável: ${action.responsavel}</span>
                                            <div class="space-x-2">
                                                <button onclick="editAction('${action.id}')" class="text-blue-600 hover:text-blue-800">
                                                    <i data-lucide="edit-2" class="w-4 h-4 inline mr-1"></i>
                                                    Editar
                                                </button>
                                                <button onclick="showFollowUpForm('${action.id}', '', 'action')" class="text-green-600 hover:text-green-800">
                                                    <i data-lucide="send" class="w-4 h-4 inline mr-1"></i>
                                                    Follow-up
                                                </button>
                                                <button onclick="deleteAction('${action.id}')" class="text-red-600 hover:text-red-800">
                                                    <i data-lucide="trash-2" class="w-4 h-4 inline mr-1"></i>
                                                    Excluir
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                `).join("")
                            }
                        </div>
                    </div>
                    
                    <!-- Follow-ups Tab -->
                    <div id="program-details-followups-content" class="p-6 hidden">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="font-semibold text-gray-900">Follow-ups do Programa</h4>
                        </div>
                        <div class="space-y-3">
                            ${programFollowUps.length === 0 ? 
                                "<p class=\"text-gray-500 text-sm\">Nenhum follow-up registrado ainda para este programa.</p>" :
                                programFollowUps.map(followUp => {
                                    const targetItem = followUp.type === "action" ? 
                                        actions.find(a => a.id == followUp.targetId) : 
                                        metas.find(m => m.id == followUp.targetId);
                                    
                                    const statusColor = followUp.status === "completed" ? "green" : 
                                                      followUp.status === "in_progress" ? "blue" : "gray";
                                    
                                    return `
                                        <div class="followup-card p-4 rounded-lg">
                                            <div class="flex justify-between items-start mb-2">
                                                <div class="flex-1">
                                                    <div class="flex items-center space-x-2 mb-1">
                                                        <span class="status-indicator status-${statusColor}"></span>
                                                        <h5 class="font-medium text-gray-900">${targetItem?.titulo || "Item não encontrado"}</h5>
                                                    </div>
                                                    <p class="text-sm text-gray-600">${followUp.mensagem}</p>
                                                    <div class="flex items-center space-x-3 text-xs text-gray-500 mt-2">
                                                        <span>Por: ${followUp.createdBy}</span>
                                                        <span>Colaboradores: ${followUp.colaboradores.length}</span>
                                                    </div>
                                                </div>
                                                <button onclick="showFollowUpTasks('${followUp.id}')" class="text-blue-600 hover:text-blue-800 text-sm">
                                                    <i data-lucide="list" class="w-4 h-4 inline mr-1"></i>
                                                    Tarefas
                                                </button>
                                            </div>
                                        </div>
                                    `;
                                }).join("")
                            }
                        </div>
                    </div>
                    
                    <!-- Inventory Tab -->
                    <div id="program-details-inventory-content" class="p-6 hidden">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="font-semibold text-gray-900">Inventário de Equipamentos</h4>
                            <button onclick="showInventarioForm('${program.name}')" class="text-purple-600 hover:text-purple-800 text-sm font-medium">
                                <i data-lucide="plus" class="w-4 h-4 inline mr-1"></i>
                                Novo Equipamento
                            </button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full table-auto">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descrição</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valor (R$)</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Atividades Relacionadas</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    ${programInventory.length === 0 ? 
                                        "<tr><td colspan=\"5\" class=\"px-4 py-8 text-center text-gray-500\">Nenhum equipamento no inventário ainda.</td></tr>" :
                                        programInventory.map(item => `
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${item.item}</td>
                                                <td class="px-4 py-4 text-sm text-gray-900">${item.descricao}</td>
                                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">R$ ${item.valor ? item.valor.toLocaleString() : "0"}</td>
                                                <td class="px-4 py-4 text-sm text-gray-900">${item.atividadesRelacionadas || "N/A"}</td>
                                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                                    <button onclick="editInventarioItem('${item.id}')" class="text-blue-600 hover:text-blue-900 mr-2">
                                                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                                                    </button>
                                                    <button onclick="deleteInventarioItem('${item.id}')" class="text-red-600 hover:text-red-900">
                                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        `).join("")
                                    }
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            `;

            const footer = `
                <div class="flex justify-end space-x-3">
                    <button onclick="closeModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors">
                        Fechar
                    </button>
                </div>
            `;

            createModal(program.name, content, footer);
            
            // IMPORTANTE: Forçar carregamento do dashboard após o modal abrir
            setTimeout(() => {
                console.log("=== FORÇANDO CARREGAMENTO INICIAL DO DASHBOARD ===");
                loadProgramDashboard(program.id);
            }, 300);
        }

        function showProgramDetailsSubTab(tabName, programId) {
            // Update tab buttons
            document.getElementById("program-details-tab-dashboard").classList.remove("border-blue-500", "text-blue-600");
            document.getElementById("program-details-tab-dashboard").classList.add("text-gray-600", "hover:text-gray-900");
            document.getElementById("program-details-tab-actions").classList.remove("border-blue-500", "text-blue-600");
            document.getElementById("program-details-tab-actions").classList.add("text-gray-600", "hover:text-gray-900");
            document.getElementById("program-details-tab-followups").classList.remove("border-blue-500", "text-blue-600");
            document.getElementById("program-details-tab-followups").classList.add("text-gray-600", "hover:text-gray-900");
            document.getElementById("program-details-tab-inventory").classList.remove("border-blue-500", "text-blue-600");
            document.getElementById("program-details-tab-inventory").classList.add("text-gray-600", "hover:text-gray-900");
            
            document.getElementById(`program-details-tab-${tabName}`).classList.add("border-blue-500", "text-blue-600");
            document.getElementById(`program-details-tab-${tabName}`).classList.remove("text-gray-600", "hover:text-gray-900");

            // Update content
            document.getElementById("program-details-dashboard-content").classList.toggle("hidden", tabName !== "dashboard");
            document.getElementById("program-details-actions-content").classList.toggle("hidden", tabName !== "actions");
            document.getElementById("program-details-followups-content").classList.toggle("hidden", tabName !== "followups");
            document.getElementById("program-details-inventory-content").classList.toggle("hidden", tabName !== "inventory");
            
            // Load dashboard content if dashboard tab is selected
            if (tabName === "dashboard") {
                console.log("Carregando dashboard do programa:", programId); // Debug
                setTimeout(() => {
                    loadProgramDashboard(programId);
                }, 100); // Pequeno delay para garantir que o DOM está pronto
            }
        }

        // Dashboard Gráfico do Programa
        function loadProgramDashboard(programId) {
            const program = programs.find(p => p.id === programId);
            if (!program) return;

            const programActions = actions.filter(a => a.programa === program.name);

            const programFollowUps = followUps.filter(f => {
                const targetItem = f.type === "action"
                    ? actions.find(a => a.id == f.targetId)
                    : metas.find(m => m.id == f.targetId);
                return targetItem && targetItem.programa === program.name;
            });

            const programTasks = tasks.filter(t => {
                const followUp = followUps.find(f => f.id === t.followUpId);
                if (!followUp) return false;
                const targetItem = followUp.type === "action"
                    ? actions.find(a => a.id == followUp.targetId)
                    : metas.find(m => m.id == followUp.targetId);
                return targetItem && targetItem.programa === program.name;
            });

            const actionsPending = programActions.filter(a => a.status === "pending").length;
            const actionsInProgress = programActions.filter(a => a.status === "in_progress").length;
            const actionsCompleted = programActions.filter(a => a.status === "completed").length;

            const tasksPending = programTasks.filter(t => t.status === "pending").length;
            const tasksInProgress = programTasks.filter(t => t.status === "in_progress").length;
            const tasksCompleted = programTasks.filter(t => t.status === "completed").length;

            const totalActions = programActions.length;
            const totalTasks = programTasks.length;
            const actionsProgress = totalActions > 0 ? Math.round((actionsCompleted / totalActions) * 100) : 0;
            const tasksProgress = totalTasks > 0 ? Math.round((tasksCompleted / totalTasks) * 100) : 0;
            const overallProgress = totalTasks > 0 ? Math.round((actionsProgress + tasksProgress) / 2) : actionsProgress;

            const container = document.getElementById("program-details-dashboard-content");
            
            container.innerHTML = `
                <div class="space-y-6">
                    <!-- Resumo Geral -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-blue-50 p-6 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-blue-600">Progresso Geral</p>
                                    <p class="text-3xl font-bold text-blue-900">${overallProgress}%</p>
                                </div>
                                <div class="bg-blue-100 p-3 rounded-lg">
                                    <i data-lucide="trending-up" class="w-6 h-6 text-blue-600"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-green-50 p-6 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-green-600">Ações Concluídas</p>
                                    <p class="text-3xl font-bold text-green-900">${actionsCompleted}</p>
                                    <p class="text-sm text-green-600">de ${totalActions} total</p>
                                </div>
                                <div class="bg-green-100 p-3 rounded-lg">
                                    <i data-lucide="check-circle" class="w-6 h-6 text-green-600"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-purple-50 p-6 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-purple-600">Tarefas Concluídas</p>
                                    <p class="text-3xl font-bold text-purple-900">${tasksCompleted}</p>
                                    <p class="text-sm text-purple-600">de ${totalTasks} total</p>
                                </div>
                                <div class="bg-purple-100 p-3 rounded-lg">
                                    <i data-lucide="list-checks" class="w-6 h-6 text-purple-600"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gráficos de Status -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Gráfico de Ações -->
                        <div class="bg-white p-6 border border-gray-200 rounded-lg">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Status das Ações</h4>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-4 h-4 bg-red-500 rounded"></div>
                                        <span class="text-sm text-gray-700">Pendentes</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm font-medium">${actionsPending}</span>
                                        <div class="w-24 bg-gray-200 rounded-full h-2">
                                            <div class="bg-red-500 h-2 rounded-full" style="width: ${totalActions > 0 ? (actionsPending / totalActions) * 100 : 0}%"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-4 h-4 bg-yellow-500 rounded"></div>
                                        <span class="text-sm text-gray-700">Em Andamento</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm font-medium">${actionsInProgress}</span>
                                        <div class="w-24 bg-gray-200 rounded-full h-2">
                                            <div class="bg-yellow-500 h-2 rounded-full" style="width: ${totalActions > 0 ? (actionsInProgress / totalActions) * 100 : 0}%"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-4 h-4 bg-green-500 rounded"></div>
                                        <span class="text-sm text-gray-700">Concluídas</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm font-medium">${actionsCompleted}</span>
                                        <div class="w-24 bg-gray-200 rounded-full h-2">
                                            <div class="bg-green-500 h-2 rounded-full" style="width: ${totalActions > 0 ? (actionsCompleted / totalActions) * 100 : 0}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Gráfico de Tarefas -->
                        <div class="bg-white p-6 border border-gray-200 rounded-lg">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Status das Tarefas</h4>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-4 h-4 bg-gray-500 rounded"></div>
                                        <span class="text-sm text-gray-700">Pendentes</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm font-medium">${tasksPending}</span>
                                        <div class="w-24 bg-gray-200 rounded-full h-2">
                                            <div class="bg-gray-500 h-2 rounded-full" style="width: ${totalTasks > 0 ? (tasksPending / totalTasks) * 100 : 0}%"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-4 h-4 bg-blue-500 rounded"></div>
                                        <span class="text-sm text-gray-700">Em Andamento</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm font-medium">${tasksInProgress}</span>
                                        <div class="w-24 bg-gray-200 rounded-full h-2">
                                            <div class="bg-blue-500 h-2 rounded-full" style="width: ${totalTasks > 0 ? (tasksInProgress / totalTasks) * 100 : 0}%"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-4 h-4 bg-green-500 rounded"></div>
                                        <span class="text-sm text-gray-700">Concluídas</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm font-medium">${tasksCompleted}</span>
                                        <div class="w-24 bg-gray-200 rounded-full h-2">
                                            <div class="bg-green-500 h-2 rounded-full" style="width: ${totalTasks > 0 ? (tasksCompleted / totalTasks) * 100 : 0}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Progresso Temporal -->
                    <div class="bg-white p-6 border border-gray-200 rounded-lg">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Progresso Geral do Programa</h4>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center text-sm text-gray-600">
                                <span>Ações (${actionsProgress}%)</span>
                                <span>${actionsCompleted}/${totalActions}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-blue-600 h-3 rounded-full transition-all duration-300" style="width: ${actionsProgress}%"></div>
                            </div>
                            
                            <div class="flex justify-between items-center text-sm text-gray-600">
                                <span>Tarefas (${tasksProgress}%)</span>
                                <span>${tasksCompleted}/${totalTasks}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-purple-600 h-3 rounded-full transition-all duration-300" style="width: ${tasksProgress}%"></div>
                            </div>
                            
                            <div class="pt-4 border-t border-gray-200">
                                <div class="flex justify-between items-center text-sm font-medium text-gray-900">
                                    <span>Progresso Total</span>
                                    <span>${overallProgress}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-4 mt-2">
                                    <div class="bg-gradient-to-r from-blue-600 to-purple-600 h-4 rounded-full transition-all duration-300" style="width: ${overallProgress}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Indicadores de Performance -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-white p-6 border border-gray-200 rounded-lg">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Follow-ups Ativos</h4>
                            <div class="text-center">
                                <div class="text-4xl font-bold text-blue-600 mb-2">${programFollowUps.length}</div>
                                <p class="text-sm text-gray-600">Follow-ups em andamento</p>
                            </div>
                        </div>
                        
                        <div class="bg-white p-6 border border-gray-200 rounded-lg">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Eficiência</h4>
                            <div class="text-center">
                                <div class="text-4xl font-bold ${overallProgress >= 75 ? 'text-green-600' : overallProgress >= 50 ? 'text-yellow-600' : 'text-red-600'} mb-2">
                                    ${overallProgress >= 75 ? 'Alta' : overallProgress >= 50 ? 'Média' : 'Baixa'}
                                </div>
                                <p class="text-sm text-gray-600">Baseado no progresso atual</p>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            lucide.createIcons();
        }

        // CORRIGIDO: Formulários funcionais
        function showMetaForm(metaId = null) {
            const meta = metaId ? metas.find(m => m.id == metaId) : null;
            const title = meta ? "Editar Meta" : "Cadastrar Nova Meta";
            
            const content = `
                <form id="metaForm" class="space-y-4">
                    <input type="hidden" id="metaId" value="${meta?.id || ""}">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Título da Meta</label>
                        <input type="text" id="metaTitulo" class="w-full p-2 border border-gray-300 rounded-lg" value="${meta?.titulo || ""}" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Objetivo Social</label>
                        <textarea id="metaObjetivo" class="w-full p-2 border border-gray-300 rounded-lg" rows="3">${meta?.objetivo || ""}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Programa Responsável</label>
                        <select id="metaPrograma" class="w-full p-2 border border-gray-300 rounded-lg">
                            ${programs.map(p => `<option value="${p.name}" ${meta?.programa === p.name ? "selected" : ""}>${p.name}</option>`).join("")}
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Indicadores (até 5)</label>
                        <div id="indicadoresContainer" class="space-y-2">
                            ${(meta?.indicadores || [""]).slice(0, 5).map((indicador, index) => `
                                <div class="flex items-center space-x-2">
                                    <input type="text" id="indicador${index}" class="flex-1 p-2 border border-gray-300 rounded-lg" 
                                           placeholder="Indicador ${index + 1}" value="${indicador}">
                                    ${index > 0 ? `<button type="button" onclick="removeIndicador(${index})" class="text-red-600 hover:text-red-800">
                                        <i data-lucide="x" class="w-4 h-4"></i>
                                    </button>` : ''}
                                </div>
                            `).join("")}
                        </div>
                        <button type="button" onclick="addIndicador()" class="mt-2 text-blue-600 hover:text-blue-800 text-sm font-medium">
                            <i data-lucide="plus" class="w-4 h-4 inline mr-1"></i>
                            Adicionar Indicador
                        </button>
                    </div>
                </form>
            `;

            const footer = `
                <button onclick="closeModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancelar</button>
                <button onclick="saveMeta()" class="gradient-bg text-white px-4 py-2 rounded-lg hover:opacity-90">Salvar Meta</button>
            `;

            createModal(title, content, footer);
        }

        function saveMeta() {
            const id = document.getElementById("metaId").value;
            const titulo = document.getElementById("metaTitulo").value;
            const objetivo = document.getElementById("metaObjetivo").value;
            const programa = document.getElementById("metaPrograma").value;
            
            // Coleta indicadores
            const indicadores = [];
            for (let i = 0; i < 5; i++) {
                const indicadorElement = document.getElementById(`indicador${i}`);
                if (indicadorElement && indicadorElement.value.trim()) {
                    indicadores.push(indicadorElement.value.trim());
                }
            }

            if (!titulo) {
                alert("O título da meta é obrigatório!");
                return;
            }

            if (id) {
                const metaIndex = metas.findIndex(m => m.id == id);
                if (metaIndex > -1) {
                    metas[metaIndex] = { ...metas[metaIndex], titulo, objetivo, programa, indicadores };
                    logActivity(`Meta "${titulo}" atualizada`, "info", "edit");
                }
            } else {
                metas.push({ id: Date.now(), titulo, objetivo, programa, indicadores, status: "active" });
                logActivity(`Nova meta "${titulo}" cadastrada`, "success", "target");
            }

            saveData();
            updateAllData();
            closeModal();
            showToast("Meta salva com sucesso!", "success");
        }

        // FUNÇÕES PARA GERENCIAR INDICADORES
        function addIndicador() {
            const container = document.getElementById("indicadoresContainer");
            const currentInputs = container.querySelectorAll('input[id^="indicador"]');
            
            if (currentInputs.length >= 5) {
                alert("Máximo de 5 indicadores por meta!");
                return;
            }
            
            const index = currentInputs.length;
            const newIndicadorHTML = `
                <div class="flex items-center space-x-2">
                    <input type="text" id="indicador${index}" class="flex-1 p-2 border border-gray-300 rounded-lg" 
                           placeholder="Indicador ${index + 1}">
                    <button type="button" onclick="removeIndicador(${index})" class="text-red-600 hover:text-red-800">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', newIndicadorHTML);
            createLucideIcons();
        }

        function removeIndicador(index) {
            const indicadorElement = document.getElementById(`indicador${index}`);
            if (indicadorElement) {
                indicadorElement.parentElement.remove();
                
                // Reindexar os indicadores restantes
                const container = document.getElementById("indicadoresContainer");
                const inputs = container.querySelectorAll('input[id^="indicador"]');
                inputs.forEach((input, newIndex) => {
                    input.id = `indicador${newIndex}`;
                    input.placeholder = `Indicador ${newIndex + 1}`;
                    
                    const removeButton = input.nextElementSibling;
                    if (removeButton && newIndex > 0) {
                        removeButton.setAttribute('onclick', `removeIndicador(${newIndex})`);
                    }
                });
            }
        }

        function showActionForm(programName = "", actionId = null) {
            const action = actionId ? actions.find(a => a.id == actionId) : null;
            const title = action ? "Editar Ação" : "Cadastrar Nova Ação";

            const content = `
                <form id="actionForm" class="space-y-4">
                    <input type="hidden" id="actionId" value="${action?.id || ""}">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Título da Ação</label>
                        <input type="text" id="actionTitulo" class="w-full p-2 border border-gray-300 rounded-lg" value="${action?.titulo || ""}" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Programa</label>
                        <select id="actionPrograma" class="w-full p-2 border border-gray-300 rounded-lg">
                            ${programs.map(p => `<option value="${p.name}" ${programName === p.name || action?.programa === p.name ? "selected" : ""}>${p.name}</option>`).join("")}
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                        <textarea id="actionDescricao" class="w-full p-2 border border-gray-300 rounded-lg" rows="3">${action?.descricao || ""}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Responsável</label>
                        <input type="text" id="actionResponsavel" class="w-full p-2 border border-gray-300 rounded-lg" value="${action?.responsavel || currentUser.name}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select id="actionStatus" class="w-full p-2 border border-gray-300 rounded-lg">
                            <option value="pending" ${action?.status === "pending" ? "selected" : ""}>Pendente</option>
                            <option value="in_progress" ${action?.status === "in_progress" ? "selected" : ""}>Em Andamento</option>
                            <option value="completed" ${action?.status === "completed" ? "selected" : ""}>Concluída</option>
                        </select>
                    </div>
                </form>
            `;

            const footer = `
                <button onclick="closeModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancelar</button>
                <button onclick="saveAction()" class="gradient-bg text-white px-4 py-2 rounded-lg hover:opacity-90">Salvar Ação</button>
            `;

            createModal(title, content, footer);
        }

        function saveAction() {
            const id = document.getElementById("actionId").value;
            const titulo = document.getElementById("actionTitulo").value;
            const programa = document.getElementById("actionPrograma").value;
            const descricao = document.getElementById("actionDescricao").value;
            const responsavel = document.getElementById("actionResponsavel").value;
            const status = document.getElementById("actionStatus").value;

            console.log("Salvando ação:", { id, titulo, programa, descricao, responsavel, status }); // Debug

            if (!titulo) {
                alert("O título da ação é obrigatório!");
                return;
            }

            if (id) {
                const actionIndex = actions.findIndex(a => a.id == id);
                if (actionIndex > -1) {
                    actions[actionIndex] = { ...actions[actionIndex], titulo, programa, descricao, responsavel, status };
                    logActivity(`Ação "${titulo}" atualizada`, "info", "edit");
                    console.log("Ação atualizada:", actions[actionIndex]); // Debug
                }
            } else {
                const newAction = { id: Date.now(), titulo, programa, descricao, responsavel, status };
                actions.push(newAction);
                logActivity(`Nova ação "${titulo}" cadastrada`, "success", "plus");
                console.log("Nova ação criada:", newAction); // Debug
            }

            saveData();
            forceUpdateAllDashboards(); // Usar nova função de atualização completa
            closeModal();
            showToast("Ação salva com sucesso!", "success");
        }

        function editAction(actionId) {
            showActionForm("", actionId);
        }

        function deleteAction(actionId) {
            console.log("Tentando excluir ação:", actionId); // Debug
            
            if (confirm("Tem certeza que deseja excluir esta ação?")) {
                const actionIndex = actions.findIndex(a => a.id == actionId);
                console.log("Índice encontrado:", actionIndex); // Debug
                
                if (actionIndex > -1) {
                    const actionTitle = actions[actionIndex].titulo;
                    actions.splice(actionIndex, 1);
                    
                    console.log("Ação excluída:", actionTitle); // Debug
                    console.log("Ações restantes:", actions.length); // Debug
                    
                    logActivity(`Ação "${actionTitle}" excluída`, "warning", "trash-2");
                    saveData();
                    forceUpdateAllDashboards(); // Usar nova função de atualização completa
                    showToast("Ação excluída com sucesso!", "success");
                    
                    // Fechar modal se estiver aberto
                    closeModal();
                } else {
                    console.error("Ação não encontrada para exclusão"); // Debug
                    showToast("Erro: Ação não encontrada!", "error");
                }
            }
        }

        function showInventarioForm(programName = "", itemId = null) {
            const item = itemId ? inventario.find(i => i.id == itemId) : null;
            const title = item ? "Editar Item do Inventário" : "Cadastrar Novo Equipamento";

            const content = `
                <form id="inventarioForm" class="space-y-4">
                    <input type="hidden" id="itemId" value="${item?.id || ""}">
                    <input type="hidden" id="itemPrograma" value="${programName || item?.programa || ""}">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Item</label>
                        <input type="text" id="itemNome" class="w-full p-2 border border-gray-300 rounded-lg" value="${item?.item || ""}" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                        <textarea id="itemDescricao" class="w-full p-2 border border-gray-300 rounded-lg" rows="3">${item?.descricao || ""}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Valor (R$)</label>
                        <input type="number" id="itemValor" class="w-full p-2 border border-gray-300 rounded-lg" value="${item?.valor || ""}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Atividades Relacionadas</label>
                        <input type="text" id="itemAtividades" class="w-full p-2 border border-gray-300 rounded-lg" value="${item?.atividadesRelacionadas || ""}">
                    </div>
                </form>
            `;

            const footer = `
                <button onclick="closeModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancelar</button>
                <button onclick="saveInventarioItem()" class="gradient-bg text-white px-4 py-2 rounded-lg hover:opacity-90">Salvar Item</button>
            `;

            createModal(title, content, footer);
        }

        function saveInventarioItem() {
            const id = document.getElementById("itemId").value;
            const programa = document.getElementById("itemPrograma").value;
            const item = document.getElementById("itemNome").value;
            const descricao = document.getElementById("itemDescricao").value;
            const valor = parseFloat(document.getElementById("itemValor").value);
            const atividadesRelacionadas = document.getElementById("itemAtividades").value;

            if (!item) {
                alert("O nome do item é obrigatório!");
                return;
            }

            if (id) {
                const itemIndex = inventario.findIndex(i => i.id == id);
                if (itemIndex > -1) {
                    inventario[itemIndex] = { ...inventario[itemIndex], item, descricao, valor, atividadesRelacionadas };
                    logActivity(`Item "${item}" atualizado no inventário`, "info", "edit");
                }
            } else {
                inventario.push({ id: Date.now(), programa, item, descricao, valor, atividadesRelacionadas });
                logActivity(`Novo item "${item}" adicionado ao inventário`, "success", "package");
            }

            saveData();
            updateAllData();
            closeModal();
            showToast("Item salvo no inventário!", "success");
        }

        function editInventarioItem(itemId) {
            showInventarioForm("", itemId);
        }

        function deleteInventarioItem(itemId) {
            console.log("Tentando excluir item do inventário:", itemId); // Debug
            
            if (confirm("Tem certeza que deseja excluir este item?")) {
                const itemIndex = inventario.findIndex(i => i.id == itemId);
                console.log("Índice encontrado:", itemIndex); // Debug
                
                if (itemIndex > -1) {
                    const itemName = inventario[itemIndex].item;
                    inventario.splice(itemIndex, 1);
                    
                    console.log("Item excluído:", itemName); // Debug
                    console.log("Itens restantes:", inventario.length); // Debug
                    
                    logActivity(`Item "${itemName}" excluído do inventário`, "warning", "trash-2");
                    saveData();
                    updateAllData();
                    showToast("Item excluído com sucesso!", "success");
                    
                    // Fechar modal e recarregar conteúdo
                    closeModal();
                    
                    // Recarregar conteúdo da aba atual
                    if (currentTab === 'programas') {
                        renderPrograms();
                    } else if (currentTab === 'meu-programa') {
                        loadMeuPrograma();
                    }
                } else {
                    console.error("Item não encontrado para exclusão"); // Debug
                    showToast("Erro: Item não encontrado!", "error");
                }
            }
        }

        function showCronogramaForm(etapaId = null) {
            const etapa = etapaId ? cronograma.find(e => e.id == etapaId) : null;
            const title = etapa ? "Editar Etapa do Cronograma" : "Nova Etapa do Cronograma";

            const content = `
                <form id="cronogramaForm" class="space-y-4">
                    <input type="hidden" id="etapaId" value="${etapa?.id || ""}">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Etapa/Programa</label>
                        <input type="text" id="etapaNome" class="w-full p-2 border border-gray-300 rounded-lg" value="${etapa?.nome || ""}" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Início</label>
                        <input type="date" id="etapaInicio" class="w-full p-2 border border-gray-300 rounded-lg" value="${etapa?.inicio || ""}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Prazo Final</label>
                        <input type="date" id="etapaFim" class="w-full p-2 border border-gray-300 rounded-lg" value="${etapa?.fim || ""}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rubrica (Valor Orçado)</label>
                        <input type="number" id="etapaRubrica" class="w-full p-2 border border-gray-300 rounded-lg" value="${etapa?.rubrica || ""}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Valor Executado</label>
                        <input type="number" id="etapaExecutado" class="w-full p-2 border border-gray-300 rounded-lg" value="${etapa?.executado || ""}">
                    </div>
                </form>
            `;

            const footer = `
                <button onclick="closeModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancelar</button>
                <button onclick="saveCronogramaEtapa()" class="gradient-bg text-white px-4 py-2 rounded-lg hover:opacity-90">Salvar Etapa</button>
            `;

            createModal(title, content, footer);
        }

        function saveCronogramaEtapa() {
            const id = document.getElementById("etapaId").value;
            const nome = document.getElementById("etapaNome").value;
            const inicio = document.getElementById("etapaInicio").value;
            const fim = document.getElementById("etapaFim").value;
            const rubrica = parseFloat(document.getElementById("etapaRubrica").value);
            const executado = parseFloat(document.getElementById("etapaExecutado").value);

            if (!nome) {
                alert("O nome da etapa é obrigatório!");
                return;
            }

            if (id) {
                const etapaIndex = cronograma.findIndex(e => e.id == id);
                if (etapaIndex > -1) {
                    cronograma[etapaIndex] = { ...cronograma[etapaIndex], nome, inicio, fim, rubrica, executado };
                    logActivity(`Etapa "${nome}" do cronograma atualizada`, "info", "edit");
                }
            } else {
                cronograma.push({ id: Date.now(), nome, inicio, fim, rubrica, executado });
                logActivity(`Nova etapa "${nome}" adicionada ao cronograma`, "success", "calendar");
            }

            saveData();
            updateAllData();
            closeModal();
            showToast("Etapa do cronograma salva com sucesso!", "success");
        }

        function editCronogramaEtapa(etapaId) {
            showCronogramaForm(etapaId);
        }

        function deleteCronogramaEtapa(etapaId) {
            console.log("Tentando excluir etapa do cronograma:", etapaId); // Debug
            
            if (confirm("Tem certeza que deseja excluir esta etapa?")) {
                const etapaIndex = cronograma.findIndex(e => e.id == etapaId);
                console.log("Índice encontrado:", etapaIndex); // Debug
                
                if (etapaIndex > -1) {
                    const etapaNome = cronograma[etapaIndex].nome;
                    cronograma.splice(etapaIndex, 1);
                    
                    console.log("Etapa excluída:", etapaNome); // Debug
                    console.log("Etapas restantes:", cronograma.length); // Debug
                    
                    logActivity(`Etapa "${etapaNome}" excluída do cronograma`, "warning", "trash-2");
                    saveData();
                    updateAllData();
                    loadCronograma(); // Recarregar especificamente o cronograma
                    showToast("Etapa excluída com sucesso!", "success");
                    
                    // Fechar modal se estiver aberto
                    closeModal();
                } else {
                    console.error("Etapa não encontrada para exclusão"); // Debug
                    showToast("Erro: Etapa não encontrada!", "error");
                }
            }
        }

        function loadMetas() {
            const container = document.getElementById("metasList");
            const followUpsContainer = document.getElementById("metasFollowUps");
            
            if (metas.length === 0) {
                container.innerHTML = "<p class=\"text-gray-500 text-sm\">Nenhuma meta cadastrada ainda.</p>";
            } else {
                container.innerHTML = metas.map(meta => `
                    <div class="p-4 border border-gray-200 rounded-lg">
                        <div class="flex justify-between items-start mb-2">
                            <h5 class="font-medium text-gray-900">${meta.titulo}</h5>
                            <div class="flex space-x-2">
                                <button onclick="showMetaForm(${meta.id})" class="text-blue-600 hover:text-blue-800 text-sm">
                                    <i data-lucide="edit-2" class="w-4 h-4 inline mr-1"></i>
                                    Editar
                                </button>
                                <button onclick="showFollowUpForm('', '${meta.id}', 'meta')" class="text-green-600 hover:text-green-800 text-sm">
                                    <i data-lucide="send" class="w-4 h-4 inline mr-1"></i>
                                    Follow-up
                                </button>
                                <button onclick="deleteMeta('${meta.id}')" class="text-red-600 hover:text-red-800 text-sm">
                                    <i data-lucide="trash-2" class="w-4 h-4 inline mr-1"></i>
                                    Excluir
                                </button>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 mb-2">${meta.objetivo}</p>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500">Programa: ${meta.programa}</span>
                            <span class="text-gray-500">Indicadores: ${meta.indicadores?.length || 0}</span>
                        </div>
                    </div>
                `).join("");
            }
            
            // Carregar follow-ups de metas
            const metaFollowUps = followUps.filter(f => f.type === "meta");
            if (metaFollowUps.length === 0) {
                followUpsContainer.innerHTML = "<p class=\"text-gray-500 text-sm\">Nenhum follow-up registrado ainda.</p>";
            } else {
                followUpsContainer.innerHTML = metaFollowUps.map(followUp => {
                    const meta = metas.find(m => m.id == followUp.targetId);
                    const statusColor = followUp.status === "completed" ? "green" : 
                                      followUp.status === "in_progress" ? "blue" : "gray";
                    
                    return `
                        <div class="followup-card p-3 rounded-lg">
                            <div class="flex items-center space-x-2 mb-2">
                                <span class="status-indicator status-${statusColor}"></span>
                                <h6 class="font-medium text-gray-900">${meta?.titulo || "Meta não encontrada"}</h6>
                            </div>
                            <p class="text-sm text-gray-600 mb-2">${followUp.mensagem}</p>
                            <div class="flex justify-between items-center text-xs text-gray-500">
                                <span>Por: ${followUp.createdBy}</span>
                                <button onclick="showFollowUpTasks('${followUp.id}')" class="text-blue-600 hover:text-blue-800">
                                    <i data-lucide="list" class="w-4 h-4 inline mr-1"></i>
                                    Tarefas
                                </button>
                            </div>
                        </div>
                    `;
                }).join("");
            }
            
            lucide.createIcons();
        }

        function deleteMeta(metaId) {
            console.log("Tentando excluir meta:", metaId); // Debug
            
            if (confirm("Tem certeza que deseja excluir esta meta?")) {
                const metaIndex = metas.findIndex(m => m.id == metaId);
                console.log("Índice encontrado:", metaIndex); // Debug
                
                if (metaIndex > -1) {
                    const metaTitle = metas[metaIndex].titulo;
                    metas.splice(metaIndex, 1);
                    
                    console.log("Meta excluída:", metaTitle); // Debug
                    console.log("Metas restantes:", metas.length); // Debug
                    
                    logActivity(`Meta "${metaTitle}" excluída`, "warning", "trash-2");
                    saveData();
                    loadMetas(); // Atualizar a lista de metas
                    updateAllData(); // Atualizar todo o dashboard
                    showToast("Meta excluída com sucesso!", "success");
                } else {
                    console.error("Meta não encontrada para exclusão"); // Debug
                    showToast("Erro: Meta não encontrada!", "error");
                }
            }
        }

        function loadCronograma() {
            const tbody = document.getElementById("cronogramaTable");
            if (cronograma.length === 0) {
                tbody.innerHTML = "<tr><td colspan=\"8\" class=\"px-4 py-8 text-center text-gray-500\">Nenhuma etapa cadastrada ainda.</td></tr>";
                return;
            }

            tbody.innerHTML = cronograma.map(etapa => {
                const saldo = (etapa.rubrica || 0) - (etapa.executado || 0);
                const status = etapa.rubrica > 0 ? Math.round(((etapa.executado || 0) / etapa.rubrica) * 100) : 0;
                return `
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${etapa.nome}</td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">${etapa.inicio}</td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">${etapa.fim}</td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">R$ ${etapa.rubrica?.toLocaleString() || "0"}</td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">R$ ${etapa.executado?.toLocaleString() || "0"}</td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium ${saldo >= 0 ? "text-green-600" : "text-red-600"}">R$ ${saldo.toLocaleString()}</td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">${status}%</td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick="editCronogramaEtapa('${etapa.id}')" class="text-blue-600 hover:text-blue-900 mr-2">
                                <i data-lucide="edit-2" class="w-4 h-4"></i>
                            </button>
                            <button onclick="deleteCronogramaEtapa('${etapa.id}')" class="text-red-600 hover:text-red-900">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </td>
                    </tr>
                `;
            }).join("");
            createLucideIcons();
        }

        function loadMeuPrograma() {
            const program = programs.find(p => p.slug === currentUser.program);
            if (!program) return;

            document.getElementById("meuProgramaTitle").textContent = `Meu Programa: ${program.name}`;
            document.getElementById("meuProgramaContent").innerHTML = `
                <div class="bg-white border border-gray-200 rounded-lg">
                    <div class="flex border-b">
                        <button onclick="showMeuProgramaSubTab('actions')" class="px-4 py-3 text-sm font-medium border-b-2 border-blue-500 text-blue-600" id="meu-programa-tab-actions">
                            <i data-lucide="activity" class="w-4 h-4 inline mr-2"></i>
                            Ações
                        </button>
                        <button onclick="showMeuProgramaSubTab('followups')" class="px-4 py-3 text-sm font-medium text-gray-600 hover:text-gray-900" id="meu-programa-tab-followups">
                            <i data-lucide="send" class="w-4 h-4 inline mr-2"></i>
                            Follow-ups
                        </button>
                        <button onclick="showMeuProgramaSubTab('inventory')" class="px-4 py-3 text-sm font-medium text-gray-600 hover:text-gray-900" id="meu-programa-tab-inventory">
                            <i data-lucide="package" class="w-4 h-4 inline mr-2"></i>
                            Inventário
                        </button>
                    </div>
                    
                    <div id="meu-programa-actions-content" class="p-6">
                        <!-- Actions content will be loaded here -->
                    </div>
                    
                    <div id="meu-programa-followups-content" class="p-6 hidden">
                        <!-- Follow-ups content will be loaded here -->
                    </div>
                    
                    <div id="meu-programa-inventory-content" class="p-6 hidden">
                        <!-- Inventory content will be loaded here -->
                    </div>
                </div>
            `;

            loadMeuProgramaActions();
            loadMeuProgramaFollowUps();
            loadMeuProgramaInventory();
        }

        function showMeuProgramaSubTab(tabName) {
            document.getElementById("meu-programa-tab-actions").classList.remove("border-blue-500", "text-blue-600");
            document.getElementById("meu-programa-tab-actions").classList.add("text-gray-600", "hover:text-gray-900");
            document.getElementById("meu-programa-tab-followups").classList.remove("border-blue-500", "text-blue-600");
            document.getElementById("meu-programa-tab-followups").classList.add("text-gray-600", "hover:text-gray-900");
            document.getElementById("meu-programa-tab-inventory").classList.remove("border-blue-500", "text-blue-600");
            document.getElementById("meu-programa-tab-inventory").classList.add("text-gray-600", "hover:text-gray-900");
            
            document.getElementById(`meu-programa-tab-${tabName}`).classList.add("border-blue-500", "text-blue-600");
            document.getElementById(`meu-programa-tab-${tabName}`).classList.remove("text-gray-600", "hover:text-gray-900");

            document.getElementById("meu-programa-actions-content").classList.toggle("hidden", tabName !== "actions");
            document.getElementById("meu-programa-followups-content").classList.toggle("hidden", tabName !== "followups");
            document.getElementById("meu-programa-inventory-content").classList.toggle("hidden", tabName !== "inventory");
        }

        function loadMeuProgramaActions() {
            const program = programs.find(p => p.slug === currentUser.program);
            if (!program) return;

            const programActions = actions.filter(a => a.programa === program.name);
            const container = document.getElementById("meu-programa-actions-content");

            container.innerHTML = `
                <div class="flex justify-between items-center mb-4">
                    <h4 class="font-semibold text-gray-900">Ações do Programa</h4>
                    <button onclick="showActionForm('${program.name}')" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        <i data-lucide="plus" class="w-4 h-4 inline mr-1"></i>
                        Nova Ação
                    </button>
                </div>
                <div class="space-y-3">
                    ${programActions.length === 0 ? 
                        "<p class=\"text-gray-500 text-sm\">Nenhuma ação cadastrada ainda.</p>" :
                        programActions.map(action => `
                            <div class="p-4 border border-gray-200 rounded-lg">
                                <div class="flex justify-between items-start mb-2">
                                    <h5 class="font-medium text-gray-900">${action.titulo}</h5>
                                    <span class="text-xs px-2 py-1 rounded-full ${getStatusColorClass(action.status)}">${translateStatus(action.status)}</span>
                                </div>
                                <p class="text-sm text-gray-600 mb-2">${action.descricao}</p>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-500">Responsável: ${action.responsavel}</span>
                                    <div class="space-x-2">
                                        <button onclick="editAction('${action.id}')" class="text-blue-600 hover:text-blue-800">
                                            <i data-lucide="edit-2" class="w-4 h-4 inline mr-1"></i>
                                            Editar
                                        </button>
                                        <button onclick="showFollowUpForm('${action.id}', '', 'action')" class="text-green-600 hover:text-green-800">
                                            <i data-lucide="send" class="w-4 h-4 inline mr-1"></i>
                                            Follow-up
                                        </button>
                                        <button onclick="deleteAction('${action.id}')" class="text-red-600 hover:text-red-800">
                                            <i data-lucide="trash-2" class="w-4 h-4 inline mr-1"></i>
                                            Excluir
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `).join("")
                    }
                </div>
                            `;
            createLucideIcons();
        }

        function loadMeuProgramaFollowUps() {
            const program = programs.find(p => p.slug === currentUser.program);
            if (!program) return;

            const programFollowUps = followUps.filter(f => {
                const targetItem = f.type === "action" ? 
                    actions.find(a => a.id == f.targetId) : 
                    metas.find(m => m.id == f.targetId);
                return targetItem && (targetItem.programa === program.name);
            });

            const container = document.getElementById("meu-programa-followups-content");

            container.innerHTML = `
                <div class="flex justify-between items-center mb-4">
                    <h4 class="font-semibold text-gray-900">Follow-ups do Programa</h4>
                    <button onclick="showProgramFollowUpForm('${program.name}')" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        <i data-lucide="plus" class="w-4 h-4 inline mr-1"></i>
                        Novo Follow-up
                    </button>
                </div>
                <div class="space-y-3">
                    ${programFollowUps.length === 0 ? 
                        "<p class=\"text-gray-500 text-sm\">Nenhum follow-up registrado ainda.</p>" :
                        programFollowUps.map(followUp => {
                            const targetItem = followUp.type === "action" ? 
                                actions.find(a => a.id == followUp.targetId) : 
                                metas.find(m => m.id == followUp.targetId);
                            
                            const statusColor = followUp.status === "completed" ? "green" : 
                                              followUp.status === "in_progress" ? "blue" : "gray";
                            
                            return `
                                <div class="followup-card p-4 rounded-lg">
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2 mb-1">
                                                <span class="status-indicator status-${statusColor}"></span>
                                                <h5 class="font-medium text-gray-900">${targetItem?.titulo || "Item não encontrado"}</h5>
                                            </div>
                                            <p class="text-sm text-gray-600">${followUp.mensagem}</p>
                                            <div class="flex items-center space-x-3 text-xs text-gray-500 mt-2">
                                                <span>Por: ${followUp.createdBy}</span>
                                                <span>Colaboradores: ${followUp.colaboradores.length}</span>
                                            </div>
                                        </div>
                                        <button onclick="showFollowUpTasks('${followUp.id}')" class="text-blue-600 hover:text-blue-800 text-sm">
                                            <i data-lucide="list" class="w-4 h-4 inline mr-1"></i>
                                            Tarefas
                                        </button>
                                    </div>
                                </div>
                            `;
                        }).join("")
                    }
                </div>
                            `;
            createLucideIcons();
        }

        function loadMeuProgramaInventory() {
            const program = programs.find(p => p.slug === currentUser.program);
            if (!program) return;

            const programInventory = inventario.filter(i => i.programa === program.name);
            const container = document.getElementById("meu-programa-inventory-content");

            container.innerHTML = `
                <div class="flex justify-between items-center mb-4">
                    <h4 class="font-semibold text-gray-900">Inventário de Equipamentos</h4>
                    <button onclick="showInventarioForm('${program.name}')" class="text-purple-600 hover:text-purple-800 text-sm font-medium">
                        <i data-lucide="plus" class="w-4 h-4 inline mr-1"></i>
                        Novo Equipamento
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full table-auto">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descrição</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valor (R$)</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Atividades Relacionadas</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            ${programInventory.length === 0 ? 
                                "<tr><td colspan=\"5\" class=\"px-4 py-8 text-center text-gray-500\">Nenhum equipamento no inventário ainda.</td></tr>" :
                                programInventory.map(item => `
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${item.item}</td>
                                        <td class="px-4 py-4 text-sm text-gray-900">${item.descricao}</td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">R$ ${item.valor ? item.valor.toLocaleString() : "0"}</td>
                                        <td class="px-4 py-4 text-sm text-gray-900">${item.atividadesRelacionadas || "N/A"}</td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                            <button onclick="editInventarioItem('${item.id}')" class="text-blue-600 hover:text-blue-900 mr-2">
                                                <i data-lucide="edit-2" class="w-4 h-4"></i>
                                            </button>
                                            <button onclick="deleteInventarioItem('${item.id}')" class="text-red-600 hover:text-red-900">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </td>
                                    </tr>
                                `).join("")
                            }
                        </tbody>
                    </table>
                </div>
                            `;
            createLucideIcons();
        }

        // Sistema de Tarefas Vinculadas
        function showProgramFollowUpForm(programName) {
            const collaborators = ["Ana Silva", "Carlos Santos", "Maria Oliveira", "João Costa", "Fernanda Lima"];
            
            const content = `
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Programa</label>
                        <input type="text" value="${programName}" readonly class="w-full p-3 border border-gray-300 rounded-lg bg-gray-50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Colaboradores</label>
                        <div class="space-y-2 max-h-32 overflow-y-auto border border-gray-300 rounded-lg p-3">
                            ${collaborators.map(collab => `
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" value="${collab}" class="collaborator-checkbox">
                                    <span class="text-sm">${collab}</span>
                                </label>
                            `).join("")}
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mensagem do Follow-up</label>
                        <textarea id="followup-message" rows="4" class="w-full p-3 border border-gray-300 rounded-lg" placeholder="Descreva o follow-up e as tarefas necessárias..."></textarea>
                    </div>
                </div>
            `;
            
            const footer = `
                <button onclick="closeModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancelar</button>
                <button onclick="saveProgramFollowUp('${programName}')" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Criar Follow-up</button>
            `;
            
            createModal("Novo Follow-up do Programa", content, footer);
        }

        function saveProgramFollowUp(programName) {
            const selectedCollaborators = Array.from(document.querySelectorAll('.collaborator-checkbox:checked')).map(cb => cb.value);
            const message = document.getElementById('followup-message').value;
            
            if (!message.trim()) {
                showToast("Por favor, insira uma mensagem para o follow-up", "error");
                return;
            }
            
            if (selectedCollaborators.length === 0) {
                showToast("Por favor, selecione pelo menos um colaborador", "error");
                return;
            }
            
            const followUp = {
                id: Date.now(),
                programa: programName,
                colaboradores: selectedCollaborators,
                mensagem: message,
                status: "pending",
                createdBy: currentUser.name,
                timestamp: new Date().toISOString(),
                type: "program"
            };
            
            followUps.push(followUp);
            saveData();
            loadMeuProgramaFollowUps();
            closeModal();
            showToast("Follow-up criado com sucesso!", "success");
        }

        // Utility functions
        function createModal(title, content, footer) {
            const modal = `
                <div class="fixed inset-0 modal-backdrop flex items-center justify-center z-50 fade-in">
                    <div class="bg-white rounded-2xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
                        <div class="flex justify-between items-center p-6 border-b">
                            <h3 class="text-lg font-semibold text-gray-900">${title}</h3>
                            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                                <i data-lucide="x" class="w-6 h-6"></i>
                            </button>
                        </div>
                        <div class="p-6">
                            ${content}
                        </div>
                        <div class="flex justify-end space-x-3 p-6 border-t bg-gray-50">
                            ${footer}
                        </div>
                    </div>
                </div>
            `;
            
            document.getElementById("modalContainer").innerHTML = modal;
            createLucideIcons();
        }

        function closeModal() {
            document.getElementById("modalContainer").innerHTML = "";
        }

        function showToast(message, type = "info") {
            const toast = document.getElementById("toast");
            const icon = document.getElementById("toastIcon");
            const messageEl = document.getElementById("toastMessage");
            
            const icons = {
                success: '<i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>',
                error: '<i data-lucide="x-circle" class="w-5 h-5 text-red-600"></i>',
                warning: '<i data-lucide="alert-triangle" class="w-5 h-5 text-yellow-600"></i>',
                info: '<i data-lucide="info" class="w-5 h-5 text-blue-600"></i>'
            };
            
            icon.innerHTML = icons[type] || icons.info;
            messageEl.textContent = message;
            
            toast.classList.add("show");
            createLucideIcons();
            
            setTimeout(() => {
                hideToast();
            }, 5000);
        }

        function hideToast() {
            document.getElementById("toast").classList.remove("show");
        }

        function showNotifications() {
            const content = `
                <div class="space-y-4">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <i data-lucide="bell" class="w-5 h-5 text-blue-600"></i>
                            <div>
                                <h4 class="font-medium text-blue-900">Novo follow-up recebido</h4>
                                <p class="text-sm text-blue-700">Você tem uma nova tarefa atribuída</p>
                                <span class="text-xs text-blue-600">Há 2 horas</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-green-50 p-4 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
                            <div>
                                <h4 class="font-medium text-green-900">Ação concluída</h4>
                                <p class="text-sm text-green-700">Implementação do sistema foi finalizada</p>
                                <span class="text-xs text-green-600">Há 1 dia</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-yellow-50 p-4 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <i data-lucide="clock" class="w-5 h-5 text-yellow-600"></i>
                            <div>
                                <h4 class="font-medium text-yellow-900">Prazo próximo</h4>
                                <p class="text-sm text-yellow-700">Meta estratégica vence em 3 dias</p>
                                <span class="text-xs text-yellow-600">Há 2 dias</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            const footer = `
                <button onclick="closeModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Fechar</button>
                <button onclick="markAllAsRead()" class="gradient-bg text-white px-4 py-2 rounded-lg hover:opacity-90">Marcar como Lidas</button>
            `;

            createModal("Notificações", content, footer);
        }

        function markAllAsRead() {
            showToast("Todas as notificações foram marcadas como lidas!", "success");
            closeModal();
        }

        function exportData() {
            const data = {
                actions,
                followUps,
                tasks,
                metas,
                cronograma,
                inventario,
                activityLog,
                exportDate: new Date().toISOString(),
                exportedBy: currentUser.name
            };
            
            const dataStr = JSON.stringify(data, null, 2);
            const dataBlob = new Blob([dataStr], {type: 'application/json'});
            const url = URL.createObjectURL(dataBlob);
            const link = document.createElement('a');
            link.href = url;
            link.download = `enedes_export_${new Date().toISOString().split('T')[0]}.json`;
            link.click();
            
            logActivity("Dados exportados", "info", "download");
            showToast("Dados exportados com sucesso!", "success");
        }

        function logout() {
            if (confirm("Tem certeza que deseja sair?")) {
                currentUser = null;
                document.getElementById("mainApp").classList.add("hidden");
                document.getElementById("loginModal").classList.remove("hidden");
                document.getElementById("userSelect").value = "";
                document.getElementById("passwordInput").value = "";
                document.getElementById("meuProgramaTab").classList.add("hidden");
                showToast("Logout realizado com sucesso!", "success");
            }
        }

        // Initialize the application
        document.addEventListener('DOMContentLoaded', function() {
            console.log("DOM carregado"); // Debug
            createLucideIcons();
            
            // Adicionar evento de Enter no campo de senha
            const passwordInput = document.getElementById("passwordInput");
            if (passwordInput) {
                passwordInput.addEventListener("keypress", function(event) {
                    if (event.key === "Enter") {
                        login();
                    }
                });
            }
            
            console.log("Sistema inicializado"); // Debug
            
            // Auto-login de teste (REMOVER EM PRODUÇÃO)
            setTimeout(() => {
                console.log("Tentando auto-login de teste...");
                document.getElementById("userSelect").value = "coord_geral";
                document.getElementById("passwordInput").value = "123456";
                // Descomente a linha abaixo para login automático:
                // login();
            }, 1000);
        });

        // Função para adicionar dados de exemplo (para testar o dashboard dinâmico)
        function adicionarDadosExemplo() {
            console.log("Adicionando dados de exemplo...");
            
            // Adicionar algumas metas de exemplo
            if (metas.length === 0) {
                metas.push(
                    { id: 1, titulo: "Aumentar número de startups", objetivo: "Crescer o ecossistema empreendedor", programa: "IFB Mais Empreendedor", indicadores: ["50 startups", "R$ 1M captado"], status: "active" },
                    { id: 2, titulo: "Capacitar empreendedores", objetivo: "Formar novos empresários", programa: "Rota Empreendedora", indicadores: ["200 pessoas", "80% aprovação"], status: "active" },
                    { id: 3, titulo: "Inovar no varejo", objetivo: "Modernizar o comércio", programa: "Lab Varejo", indicadores: ["10 soluções", "5 parcerias"], status: "active" }
                );
            }
            
            // Adicionar algumas ações de exemplo
            if (actions.length === 0) {
                actions.push(
                    { id: 1, titulo: "Workshop de Pitch", programa: "IFB Mais Empreendedor", descricao: "Evento para treinar apresentações", responsavel: "João Silva", status: "completed" },
                    { id: 2, titulo: "Mentoria Individual", programa: "IFB Mais Empreendedor", descricao: "Sessões de mentoria 1:1", responsavel: "Maria Santos", status: "in_progress" },
                    { id: 3, titulo: "Pesquisa de Mercado", programa: "Rota Empreendedora", descricao: "Análise do comportamento do consumidor", responsavel: "Pedro Costa", status: "pending" },
                    { id: 4, titulo: "Produção de Vídeo", programa: "Rota Empreendedora", descricao: "Criar conteúdo audiovisual", responsavel: "Ana Lima", status: "in_progress" },
                    { id: 5, titulo: "Desenvolvimento App", programa: "Lab Varejo", descricao: "Criar aplicativo mobile", responsavel: "Carlos Tech", status: "pending" },
                    { id: 6, titulo: "Análise de Dados", programa: "Lab Consumer", descricao: "Análise de comportamento", responsavel: "Sofia Data", status: "completed" },
                    { id: 7, titulo: "Gravação Podcast", programa: "Estúdio", descricao: "Série de podcasts educativos", responsavel: "Ricardo Audio", status: "in_progress" },
                    { id: 8, titulo: "Sistema de Gestão", programa: "IFB Digital", descricao: "Desenvolvimento de sistema", responsavel: "Tech Team", status: "pending" },
                    { id: 9, titulo: "Interface Interativa", programa: "Sala Interativa", descricao: "Nova interface touch", responsavel: "UX Designer", status: "completed" },
                    { id: 10, titulo: "Campanha Digital", programa: "Agência de Marketing", descricao: "Campanha nas redes sociais", responsavel: "Marketing Team", status: "in_progress" }
                );
            }
            
            // Adicionar alguns follow-ups de exemplo
            if (followUps.length === 0) {
                followUps.push(
                    { id: 1, targetId: 1, type: "action", mensagem: "Verificar resultados do workshop", prioridade: "alta", status: "pending", createdBy: "Coordenação", colaboradores: [{name: "João Silva"}], createdAt: new Date().toISOString() },
                    { id: 2, targetId: 2, type: "action", mensagem: "Agendar próximas sessões", prioridade: "media", status: "in_progress", createdBy: "Coordenação", colaboradores: [{name: "Maria Santos"}], createdAt: new Date().toISOString() },
                    { id: 3, targetId: 1, type: "meta", mensagem: "Revisar indicadores da meta", prioridade: "alta", status: "completed", createdBy: "Coordenação", colaboradores: [{name: "João Silva"}], createdAt: new Date().toISOString() }
                );
            }
            
            // Adicionar algumas tarefas de exemplo
            if (tasks.length === 0) {
                tasks.push(
                    { id: 1, followUpId: 1, titulo: "Consolidar relatório", descricao: "Compilar dados do workshop", responsavel: "João Silva", status: "pending", prazo: "2025-01-20", createdAt: new Date().toISOString() },
                    { id: 2, followUpId: 2, titulo: "Contatar mentores", descricao: "Agendar próximas sessões", responsavel: "Maria Santos", status: "in_progress", prazo: "2025-01-15", createdAt: new Date().toISOString() },
                    { id: 3, followUpId: 3, titulo: "Análise de KPIs", descricao: "Verificar evolução dos indicadores", responsavel: "João Silva", status: "completed", prazo: "2025-01-10", createdAt: new Date().toISOString() }
                );
            }
            
            // Adicionar itens ao inventário
            if (inventario.length === 0) {
                inventario.push(
                    { id: 1, programa: "Lab Varejo", item: "Computador Dell", descricao: "Desktop para desenvolvimento", valor: 3500, atividadesRelacionadas: "Desenvolvimento de soluções" },
                    { id: 2, programa: "Estúdio", item: "Câmera Canon", descricao: "Câmera profissional para gravações", valor: 8000, atividadesRelacionadas: "Produção audiovisual" },
                    { id: 3, programa: "IFB Digital", item: "Tablet Samsung", descricao: "Para testes de aplicativos", valor: 1200, atividadesRelacionadas: "Testes de usabilidade" }
                );
            }
            
            // Adicionar etapas ao cronograma
            if (cronograma.length === 0) {
                cronograma.push(
                    { id: 1, nome: "Fase 1 - Planejamento", inicio: "2025-01-01", fim: "2025-03-31", rubrica: 50000, executado: 35000 },
                    { id: 2, nome: "Fase 2 - Execução", inicio: "2025-04-01", fim: "2025-09-30", rubrica: 120000, executado: 80000 },
                    { id: 3, nome: "Fase 3 - Monitoramento", inicio: "2025-10-01", fim: "2025-12-31", rubrica: 30000, executado: 10000 }
                );
            }
            
            saveData();
            console.log("Dados de exemplo adicionados:", {
                metas: metas.length,
                actions: actions.length, 
                followUps: followUps.length,
                tasks: tasks.length,
                inventario: inventario.length,
                cronograma: cronograma.length
            });
        }

        // Função para limpar todos os dados
        function limparTodosDados() {
            if (confirm("Tem certeza que deseja limpar TODOS os dados? Esta ação não pode ser desfeita!")) {
                actions = [];
                followUps = [];
                tasks = [];
                metas = [];
                cronograma = [];
                inventario = [];
                activityLog = [];
                
                saveData();
                console.log("Todos os dados foram limpos");
            }
        }

        // Make all functions globally available for onclick handlers
        window.login = login;
        window.logout = logout;
        window.showTab = showTab;
        window.showMetaForm = showMetaForm;
        window.showActionForm = showActionForm;
        window.showFollowUpForm = showFollowUpForm;
        window.showNotifications = showNotifications;
        window.exportData = exportData;
        window.closeModal = closeModal;
        window.hideToast = hideToast;
        window.saveMeta = saveMeta;
        window.saveAction = saveAction;
        window.sendFollowUp = sendFollowUp;
        window.showMetasSubTab = showMetasSubTab;
        window.deleteMeta = deleteMeta;
        window.deleteAction = deleteAction;
        window.editAction = editAction;
        window.markAllAsRead = markAllAsRead;
        window.showProgramDetails = showProgramDetails;
        window.showProgramDetailsSubTab = showProgramDetailsSubTab;
        window.showInventarioForm = showInventarioForm;
        window.saveInventarioItem = saveInventarioItem;
        window.editInventarioItem = editInventarioItem;
        window.deleteInventarioItem = deleteInventarioItem;
        window.showCronogramaForm = showCronogramaForm;
        window.saveCronogramaEtapa = saveCronogramaEtapa;
        window.editCronogramaEtapa = editCronogramaEtapa;
        window.deleteCronogramaEtapa = deleteCronogramaEtapa;
        window.showFollowUpTasks = showFollowUpTasks;
        window.showTaskForm = showTaskForm;
        window.saveTask = saveTask;
        window.updateTaskStatus = updateTaskStatus;
        window.confirmTaskStatusUpdate = confirmTaskStatusUpdate;
        window.deleteTask = deleteTask;
        window.updateFollowUpStatus = updateFollowUpStatus;
        window.confirmFollowUpStatusUpdate = confirmFollowUpStatusUpdate;
        window.filterFollowUps = filterFollowUps;
        window.addIndicador = addIndicador;
        window.removeIndicador = removeIndicador;
        window.updateFollowUpType = updateFollowUpType;
        window.showMeuProgramaSubTab = showMeuProgramaSubTab;
        window.showProgramFollowUpForm = showProgramFollowUpForm;
        window.saveProgramFollowUp = saveProgramFollowUp;
        window.adicionarDadosExemplo = adicionarDadosExemplo;
        window.filterFollowUps = filterFollowUps;
        window.updateAllData = updateAllData;
        window.addTaskToFollowUp = addTaskToFollowUp;
        window.removeTaskFromFollowUp = removeTaskFromFollowUp;
        window.collectFollowUpTasks = collectFollowUpTasks;
        window.limparTodosDados = limparTodosDados;
        window.loadProgramDashboard = loadProgramDashboard;
    </script>
</body>
</html>
