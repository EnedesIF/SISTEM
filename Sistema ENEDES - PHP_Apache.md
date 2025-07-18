# Sistema ENEDES - PHP/Apache

Sistema completo de gestão de projetos ENEDES desenvolvido em PHP com Apache, compatível com Render.com e Neon.tech.

## 🚀 Funcionalidades

- **Dashboard Executivo**: Visão geral com métricas e estatísticas
- **Gestão de Metas**: Cadastro e acompanhamento de metas estratégicas
- **Cronograma**: Controle de etapas e prazos de execução
- **Programas**: Gestão completa de programas com ações e inventário
- **Follow-ups**: Sistema de acompanhamento com tarefas vinculadas
- **Sistema de Tarefas**: Criação e gerenciamento de tarefas por follow-up
- **Inventário**: Controle de equipamentos e recursos por programa
- **Controle de Acesso**: Perfis diferenciados (Coordenador Geral e Coordenadores de Programa)

## 🛠️ Tecnologias

- **Backend**: PHP 8.1 com Apache
- **Frontend**: HTML5, CSS3, JavaScript, Tailwind CSS
- **Banco de Dados**: PostgreSQL (Neon.tech)
- **Deploy**: Render.com com Docker
- **Containerização**: Docker com php:8.1-apache

## 📋 Pré-requisitos

- Git
- Conta no [Render.com](https://render.com)
- Conta no [Neon.tech](https://neon.tech) (para banco de dados PostgreSQL)

## 🌐 Deploy no Render

### 1. Preparação do Banco de Dados (Neon.tech)

1. Acesse [Neon.tech](https://neon.tech) e crie uma conta
2. Crie um novo projeto/banco de dados
3. Copie a string de conexão PostgreSQL (formato: `postgresql://username:password@hostname:port/database`)

### 2. Deploy no Render

1. Faça push do código para um repositório Git (GitHub, GitLab, etc.)
2. Acesse [Render.com](https://render.com) e crie uma conta
3. Clique em "New +" → "Web Service"
4. Conecte seu repositório Git
5. Configure o serviço:
   - **Name**: `enedessystem` (ou nome de sua escolha)
   - **Environment**: `Docker`
   - **Plan**: `Free`
6. O Render detectará automaticamente o `Dockerfile` e `render.yaml`
7. Configure a variável de ambiente:
   - `DATABASE_URL`: String de conexão do Neon.tech
8. Clique em "Create Web Service"

### 3. Configuração Automática

O arquivo `render.yaml` está configurado para:
- Build automático via Docker
- Configuração de banco PostgreSQL
- Exposição da porta 80
- Variáveis de ambiente automáticas

## 🔧 Desenvolvimento Local

### Usando Docker (Recomendado)

```bash
# Clone o repositório
git clone <url-do-repositorio>
cd enedes-php-system

# Build da imagem Docker
docker build -t enedes-system .

# Execute o container
docker run -p 8080:80 -e DATABASE_URL="postgresql://user:pass@host:port/db" enedes-system
```

### Usando XAMPP/WAMP (Alternativo)

1. Instale XAMPP ou WAMP
2. Copie os arquivos para a pasta `htdocs` ou `www`
3. Configure o banco PostgreSQL
4. Acesse `http://localhost/enedes-php-system`

## 👥 Usuários Padrão

O sistema inicializa automaticamente com os seguintes usuários:

| Usuário | Senha | Perfil |
|---------|-------|--------|
| Coordenação Geral | 123456 | Coordenador Geral |
| IFB Mais Empreendedor | 123456 | Coordenador de Programa |
| Rota Empreendedora | 123456 | Coordenador de Programa |
| Lab Varejo | 123456 | Coordenador de Programa |
| Lab Consumer | 123456 | Coordenador de Programa |
| Estúdio | 123456 | Coordenador de Programa |
| IFB Digital | 123456 | Coordenador de Programa |
| Sala Interativa | 123456 | Coordenador de Programa |
| Agência de Marketing | 123456 | Coordenador de Programa |

## 📊 Estrutura do Banco de Dados

### Principais Tabelas

- **users**: Usuários do sistema com roles e programas
- **metas**: Metas estratégicas do projeto
- **programas**: Programas disponíveis no sistema
- **acoes**: Ações específicas por programa
- **followups**: Follow-ups do sistema
- **tasks**: Tarefas vinculadas aos follow-ups
- **inventario**: Inventário de equipamentos por programa
- **etapas**: Cronograma de execução por programa

## 🔐 Controle de Acesso

### Coordenador Geral
- Acesso completo a todos os programas
- Visualização de todas as metas e ações
- Dashboard executivo completo
- Gestão de todos os follow-ups

### Coordenador de Programa
- Acesso restrito ao seu programa específico
- Gestão de ações e inventário do programa
- Follow-ups específicos do programa
- Dashboard do programa individual

## 📱 API Endpoints

### Autenticação
- `POST /api.php?endpoint=login` - Login no sistema
- `POST /api.php?endpoint=logout` - Logout
- `GET /api.php?endpoint=me` - Dados do usuário atual

### Metas
- `GET /api.php?endpoint=metas` - Listar metas
- `POST /api.php?endpoint=metas` - Criar nova meta
- `POST /api.php?endpoint=delete_meta` - Excluir meta

### Programas
- `GET /api.php?endpoint=programas` - Listar programas
- `GET /api.php?endpoint=acoes?programa=NOME` - Ações do programa

### Ações
- `GET /api.php?endpoint=acoes` - Listar ações
- `POST /api.php?endpoint=acoes` - Criar nova ação

### Follow-ups
- `GET /api.php?endpoint=followups` - Listar follow-ups
- `POST /api.php?endpoint=followups` - Criar follow-up

### Tarefas
- `GET /api.php?endpoint=tasks?followup_id=ID` - Tarefas do follow-up
- `POST /api.php?endpoint=tasks` - Criar nova tarefa

### Inventário
- `GET /api.php?endpoint=inventario?programa=NOME` - Inventário do programa
- `POST /api.php?endpoint=inventario` - Adicionar item ao inventário

### Cronograma
- `GET /api.php?endpoint=etapas` - Listar etapas
- `POST /api.php?endpoint=etapas` - Criar nova etapa

### Dashboard
- `GET /api.php?endpoint=dashboard` - Estatísticas gerais

## 🐛 Solução de Problemas

### Erro 404 na API
1. Verifique se o arquivo `api.php` está na raiz do projeto
2. Confirme se o Apache está configurado corretamente
3. Verifique se o mod_rewrite está habilitado

### Erro de Conexão com Banco
1. Verifique se a `DATABASE_URL` está correta no formato PostgreSQL
2. Confirme se o banco Neon.tech está ativo e acessível
3. Teste a conexão com as credenciais fornecidas

### Erro de Permissões
```bash
# No container Docker
chmod -R 755 /var/www/html
chown -R www-data:www-data /var/www/html
```

### Logs de Debug
- Verifique os logs do Apache: `/var/log/apache2/error.log`
- Logs PHP: `/var/log/php_errors.log`
- Logs do Render: Disponíveis no dashboard do Render

## 🔄 Atualizações

Para atualizar o sistema:

1. Faça push das alterações para o repositório Git
2. O Render fará deploy automático das mudanças
3. O banco de dados será atualizado automaticamente na primeira execução

## 📞 Suporte

Para suporte técnico ou dúvidas sobre o sistema:
- Verifique os logs de erro
- Consulte a documentação da API
- Entre em contato com a equipe de desenvolvimento

## 🚀 URLs de Teste

Após o deploy no Render, teste os seguintes endpoints:

- **Sistema**: `https://enedessystem.onrender.com/`
- **API de Metas**: `https://enedessystem.onrender.com/api.php?endpoint=goals`
- **Login**: `https://enedessystem.onrender.com/api.php?endpoint=login`
- **Dashboard**: `https://enedessystem.onrender.com/api.php?endpoint=dashboard`

## 📄 Licença

Este projeto é propriedade do ENEDES - Todos os direitos reservados.

---

**Sistema ENEDES PHP** - Gestão Completa de Projetos v1.0

