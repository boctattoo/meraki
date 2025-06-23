<?php
/**
 * Arquivo de Configuração do Sistema Meraki - Kanban
 * Microlins Bauru
 */

// Configurações do Sistema
define('SISTEMA_NOME', 'Sistema Meraki - Kanban');
define('SISTEMA_VERSAO', '1.0.0');
define('EMPRESA_NOME', 'Microlins Bauru');
define('EMPRESA_ENDERECO', 'Rua Agenor Meira, 451 – Centro, Bauru/SP');

// Configurações de Sessão
define('SESSAO_TIMEOUT', 7200); // 2 horas em segundos
define('SESSAO_NOME', 'MERAKI_SESSION');

// Configurações de Segurança
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_TIME', 300); // 5 minutos em segundos
define('CSRF_TOKEN_EXPIRE', 3600); // 1 hora

// Configurações do Kanban
define('STATUS_TAREFAS', [
    'afazer' => 'A Fazer',
    'progresso' => 'Em Progresso', 
    'concluido' => 'Concluído'
]);

define('PRIORIDADES_TAREFAS', [
    'Baixa' => 'success',
    'Média' => 'warning',
    'Alta' => 'danger'
]);

define('CORES_STATUS', [
    'afazer' => '#6c757d',
    'progresso' => '#fd7e14',
    'concluido' => '#28a745'
]);

// Configurações de Upload
define('MAX_FILE_SIZE', 5242880); // 5MB em bytes
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx']);
define('UPLOAD_PATH', 'uploads/');

// Configurações de Email (se necessário)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', '');
define('SMTP_PASSWORD', '');
define('SMTP_FROM_EMAIL', 'sistema@microlinsbautu.com.br');
define('SMTP_FROM_NAME', 'Sistema Meraki');

// Configurações de Paginação
define('ITENS_POR_PAGINA', 20);
define('MAX_ITENS_POR_PAGINA', 100);

// Configurações de Log
define('LOG_LEVEL', 'INFO'); // DEBUG, INFO, WARNING, ERROR
define('LOG_PATH', 'logs/');
define('LOG_MAX_SIZE', 10485760); // 10MB

// Configurações de Cache
define('CACHE_ENABLED', true);
define('CACHE_TIME', 300); // 5 minutos
define('CACHE_PATH', 'cache/');

// URLs do Sistema
define('BASE_URL', 'http://localhost/meraki/');
define('ASSETS_URL', BASE_URL . 'assets/');

// Timezone
date_default_timezone_set('America/Sao_Paulo');

// Configurações de Desenvolvimento
define('DEBUG_MODE', false);
define('SHOW_ERRORS', false);

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Configurações do Banco de Dados (pode ser sobrescrito em conexao.php)
define('DB_HOST', 'localhost');
define('DB_NAME', 'microl68_meraki');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Mensagens do Sistema
define('MENSAGENS', [
    'login_sucesso' => 'Login realizado com sucesso!',
    'login_erro' => 'Usuário ou senha incorretos.',
    'sessao_expirada' => 'Sua sessão expirou. Faça login novamente.',
    'permissao_negada' => 'Você não tem permissão para acessar esta funcionalidade.',
    'tarefa_criada' => 'Tarefa criada com sucesso!',
    'tarefa_atualizada' => 'Tarefa atualizada com sucesso!',
    'tarefa_excluida' => 'Tarefa excluída com sucesso!',
    'erro_generico' => 'Ocorreu um erro inesperado. Tente novamente.',
    'campos_obrigatorios' => 'Preencha todos os campos obrigatórios.',
    'arquivo_muito_grande' => 'Arquivo muito grande. Tamanho máximo: ' . (MAX_FILE_SIZE / 1024 / 1024) . 'MB',
    'extensao_nao_permitida' => 'Extensão de arquivo não permitida.',
]);

// Configurações de Notificações
define('NOTIFICACOES_ENABLED', true);
define('NOTIFICAR_PRAZOS', true);
define('DIAS_AVISO_PRAZO', 3);

// Configurações de Backup
define('BACKUP_ENABLED', true);
define('BACKUP_PATH', 'backups/');
define('BACKUP_KEEP_DAYS', 30);

// Roles/Cargos do Sistema
define('ROLES', [
    'admin' => 'Administrador',
    'coordenador' => 'Coordenador',
    'instrutor' => 'Instrutor',
    'usuario' => 'Usuário'
]);

// Permissões por Role
define('PERMISSOES', [
    'admin' => [
        'kanban_view', 'kanban_create', 'kanban_edit', 'kanban_delete',
        'usuarios_manage', 'sistema_config', 'relatorios_view'
    ],
    'coordenador' => [
        'kanban_view', 'kanban_create', 'kanban_edit', 'kanban_delete',
        'relatorios_view'
    ],
    'instrutor' => [
        'kanban_view', 'kanban_create', 'kanban_edit'
    ],
    'usuario' => [
        'kanban_view', 'kanban_create', 'kanban_edit'
    ]
]);

// Configurações de API (se necessário)
define('API_ENABLED', false);
define('API_KEY_LENGTH', 32);
define('API_RATE_LIMIT', 100); // requests por minuto

// Headers de Segurança
if (!DEBUG_MODE) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}

// Função para verificar se uma funcionalidade está habilitada
function isFeatureEnabled($feature) {
    $features = [
        'notificacoes' => NOTIFICACOES_ENABLED,
        'backup' => BACKUP_ENABLED,
        'cache' => CACHE_ENABLED,
        'api' => API_ENABLED
    ];
    
    return isset($features[$feature]) ? $features[$feature] : false;
}

// Função para obter configuração
function getConfig($key, $default = null) {
    return defined($key) ? constant($key) : $default;
}

// Função para verificar permissão
function hasPermission($permission, $userRole = null) {
    if (!$userRole && isset($_SESSION['cargo'])) {
        $userRole = $_SESSION['cargo'];
    }
    
    if (!$userRole || !isset(PERMISSOES[$userRole])) {
        return false;
    }
    
    return in_array($permission, PERMISSOES[$userRole]);
}

// Autoload de classes (se necessário)
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/classes/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});
?>