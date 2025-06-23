<?php
/**
 * Sistema de Instalação - Meraki Kanban
 * Microlins Bauru
 */

// Verificar se já foi instalado
if (file_exists('config_installed.lock')) {
    die('Sistema já foi instalado. Para reinstalar, remova o arquivo config_installed.lock');
}

$errors = [];
$success = [];
$step = $_GET['step'] ?? 1;

// Verificações de pré-requisitos
function checkPrerequisites() {
    $checks = [];
    
    // PHP Version
    $checks['php_version'] = [
        'name' => 'Versão do PHP (>= 7.4)',
        'status' => version_compare(PHP_VERSION, '7.4.0', '>='),
        'current' => PHP_VERSION
    ];
    
    // Extensions
    $required_extensions = ['pdo', 'pdo_mysql', 'json', 'mbstring'];
    foreach ($required_extensions as $ext) {
        $checks["ext_{$ext}"] = [
            'name' => "Extensão {$ext}",
            'status' => extension_loaded($ext),
            'current' => extension_loaded($ext) ? 'Carregada' : 'Não encontrada'
        ];
    }
    
    // Permissions
    $required_dirs = ['uploads/', 'logs/', 'cache/', 'backups/'];
    foreach ($required_dirs as $dir) {
        $exists = is_dir($dir);
        $writable = $exists ? is_writable($dir) : false;
        
        $checks["dir_{$dir}"] = [
            'name' => "Diretório {$dir}",
            'status' => $exists && $writable,
            'current' => $exists ? ($writable ? 'Gravável' : 'Não gravável') : 'Não existe'
        ];
    }
    
    return $checks;
}

function createDirectories() {
    $dirs = ['uploads/', 'logs/', 'cache/', 'backups/', 'assets/css/', 'assets/js/'];
    
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
}

function testDatabaseConnection($host, $dbname, $username, $password) {
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return ['success' => true, 'pdo' => $pdo];
    } catch (PDOException $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

function createDatabaseTables($pdo) {
    $sql = "
    -- Tabela de configurações
    CREATE TABLE IF NOT EXISTS `configuracoes` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `chave` varchar(100) NOT NULL,
        `valor` text,
        `descricao` text,
        `tipo` enum('string','number','boolean','json') DEFAULT 'string',
        `modificado_por` int(11) DEFAULT NULL,
        `data_modificacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `chave` (`chave`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

    -- Dados iniciais das configurações
    INSERT INTO `configuracoes` (`chave`, `valor`, `descricao`, `tipo`) VALUES
    ('sistema_instalado', 'true', 'Sistema foi instalado com sucesso', 'boolean'),
    ('sistema_versao', '1.0.0', 'Versão atual do sistema', 'string'),
    ('empresa_nome', 'Microlins Bauru', 'Nome da empresa', 'string'),
    ('empresa_endereco', 'Rua Agenor Meira, 451 – Centro, Bauru/SP', 'Endereço da empresa', 'string'),
    ('max_login_attempts', '5', 'Máximo de tentativas de login', 'number'),
    ('session_timeout', '7200', 'Timeout da sessão em segundos', 'number'),
    ('enable_notifications', 'true', 'Ativar sistema de notificações', 'boolean'),
    ('maintenance_mode', 'false', 'Modo de manutenção', 'boolean');
    ";
    
    $pdo->exec($sql);
}

function createAdminUser($pdo, $username, $password, $name, $email) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO usuarios (user, passa, nome, email, cargo, ativo) VALUES (?, ?, ?, ?, 'admin', 1)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username, $hashedPassword, $name, $email]);
    
    return $pdo->lastInsertId();
}

// Processar formulários
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($_POST['action']) {
        case 'check_requirements':
            $step = 2;
            break;
            
        case 'test_database':
            $host = $_POST['db_host'];
            $dbname = $_POST['db_name'];
            $username = $_POST['db_user'];
            $password = $_POST['db_pass'];
            
            $result = testDatabaseConnection($host, $dbname, $username, $password);
            
            if ($result['success']) {
                // Salvar configurações de banco
                $_SESSION['db_config'] = [
                    'host' => $host,
                    'dbname' => $dbname,
                    'username' => $username,
                    'password' => $password
                ];
                $success[] = 'Conexão com banco de dados estabelecida com sucesso!';
                $step = 3;
            } else {
                $errors[] = 'Erro ao conectar com banco de dados: ' . $result['error'];
            }
            break;
            
        case 'install_system':
            session_start();
            
            if (!isset($_SESSION['db_config'])) {
                $errors[] = 'Configuração do banco de dados perdida. Reinicie a instalação.';
                break;
            }
            
            $dbConfig = $_SESSION['db_config'];
            $dbResult = testDatabaseConnection($dbConfig['host'], $dbConfig['dbname'], $dbConfig['username'], $dbConfig['password']);
            
            if (!$dbResult['success']) {
                $errors[] = 'Erro na conexão com banco de dados.';
                break;
            }
            
            try {
                $pdo = $dbResult['pdo'];
                
                // Criar diretórios
                createDirectories();
                
                // Criar tabelas (se necessário)
                createDatabaseTables($pdo);
                
                // Criar usuário admin
                $adminId = createAdminUser(
                    $pdo,
                    $_POST['admin_username'],
                    $_POST['admin_password'],
                    $_POST['admin_name'],
                    $_POST['admin_email']
                );
                
                // Criar arquivo de configuração
                $configContent = "<?php
// Configurações do Banco de Dados - Gerado automaticamente
define('DB_HOST', '{$dbConfig['host']}');
define('DB_NAME', '{$dbConfig['dbname']}');
define('DB_USER', '{$dbConfig['username']}');
define('DB_PASS', '{$dbConfig['password']}');
define('DB_CHARSET', 'utf8mb4');

// Configuração de instalação
define('SISTEMA_INSTALADO', true);
define('DATA_INSTALACAO', '" . date('Y-m-d H:i:s') . "');
?>";
                
                file_put_contents('config_db.php', $configContent);
                
                // Criar arquivo de lock
                file_put_contents('config_installed.lock', date('Y-m-d H:i:s'));
                
                $success[] = 'Sistema instalado com sucesso!';
                $step = 4;
                
            } catch (Exception $e) {
                $errors[] = 'Erro durante a instalação: ' . $e->getMessage();
            }
            break;
    }
}

session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalação - Sistema Meraki Kanban</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
        }
        .install-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        .install-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .install-header {
            background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .install-body {
            padding: 2rem;
        }
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
        }
        .step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e2e8f0;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 0.5rem;
            font-weight: bold;
            position: relative;
        }
        .step.active {
            background: #3b82f6;
            color: white;
        }
        .step.completed {
            background: #10b981;
            color: white;
        }
        .step:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 100%;
            width: 1rem;
            height: 2px;
            background: #e2e8f0;
            transform: translateY(-50%);
        }
        .requirement-item {
            display: flex;
            align-items: center;
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            border-radius: 8px;
            background: #f8fafc;
        }
        .requirement-item.success {
            background: #f0fdf4;
            border-left: 4px solid #10b981;
        }
        .requirement-item.error {
            background: #fef2f2;
            border-left: 4px solid #ef4444;
        }
        .requirement-status {
            margin-right: 1rem;
            width: 24px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="install-container">
        <div class="install-card">
            <div class="install-header">
                <h1><i class="fas fa-graduation-cap"></i> Sistema Meraki - Kanban</h1>
                <p class="mb-0">Assistente de Instalação - Microlins Bauru</p>
            </div>
            
            <div class="install-body">
                <!-- Indicador de Passos -->
                <div class="step-indicator">
                    <div class="step <?= $step >= 1 ? ($step > 1 ? 'completed' : 'active') : '' ?>">1</div>
                    <div class="step <?= $step >= 2 ? ($step > 2 ? 'completed' : 'active') : '' ?>">2</div>
                    <div class="step <?= $step >= 3 ? ($step > 3 ? 'completed' : 'active') : '' ?>">3</div>
                    <div class="step <?= $step >= 4 ? 'active' : '' ?>">4</div>
                </div>

                <!-- Exibir erros -->
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <h6><i class="fas fa-exclamation-circle"></i> Erros encontrados:</h6>
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <!-- Exibir sucessos -->
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success">
                        <h6><i class="fas fa-check-circle"></i> Sucesso:</h6>
                        <ul class="mb-0">
                            <?php foreach ($success as $msg): ?>
                                <li><?= htmlspecialchars($msg) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <!-- Passo 1: Verificação de Pré-requisitos -->
                <?php if ($step == 1): ?>
                    <h3><i class="fas fa-clipboard-check"></i> Verificação de Pré-requisitos</h3>
                    <p class="text-muted">Verificando se o servidor atende aos requisitos mínimos...</p>
                    
                    <?php $checks = checkPrerequisites(); $allPassed = true; ?>
                    
                    <?php foreach ($checks as $check): ?>
                        <div class="requirement-item <?= $check['status'] ? 'success' : 'error' ?>">
                            <div class="requirement-status">
                                <i class="fas fa-<?= $check['status'] ? 'check text-success' : 'times text-danger' ?>"></i>
                            </div>
                            <div class="flex-grow-1">
                                <strong><?= $check['name'] ?>:</strong> <?= $check['current'] ?>
                            </div>
                        </div>
                        <?php if (!$check['status']) $allPassed = false; ?>
                    <?php endforeach; ?>
                    
                    <?php if ($allPassed): ?>
                        <form method="POST" class="mt-4">
                            <input type="hidden" name="action" value="check_requirements">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-arrow-right"></i> Próximo: Configurar Banco de Dados
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-warning mt-4">
                            <h6><i class="fas fa-exclamation-triangle"></i> Ação Necessária</h6>
                            <p>Corrija os problemas listados acima antes de continuar com a instalação.</p>
                            <button onclick="location.reload()" class="btn btn-warning">
                                <i class="fas fa-sync"></i> Verificar Novamente
                            </button>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- Passo 2: Configuração do Banco de Dados -->
                <?php if ($step == 2): ?>
                    <h3><i class="fas fa-database"></i> Configuração do Banco de Dados</h3>
                    <p class="text-muted">Configure a conexão com o banco de dados MySQL.</p>
                    
                    <form method="POST">
                        <input type="hidden" name="action" value="test_database">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Host do Banco</label>
                                <input type="text" class="form-control" name="db_host" value="localhost" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nome do Banco</label>
                                <input type="text" class="form-control" name="db_name" value="microl68_meraki" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Usuário</label>
                                <input type="text" class="form-control" name="db_user" value="root" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Senha</label>
                                <input type="password" class="form-control" name="db_pass">
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Importante:</strong> Certifique-se de que o banco de dados já existe e que o usuário tem permissões para criar tabelas.
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-plug"></i> Testar Conexão
                        </button>
                    </form>
                <?php endif; ?>

                <!-- Passo 3: Configuração do Administrador -->
                <?php if ($step == 3): ?>
                    <h3><i class="fas fa-user-shield"></i> Criar Usuário Administrador</h3>
                    <p class="text-muted">Crie a conta do administrador do sistema.</p>
                    
                    <form method="POST">
                        <input type="hidden" name="action" value="install_system">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nome de Usuário</label>
                                <input type="text" class="form-control" name="admin_username" value="admin" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nome Completo</label>
                                <input type="text" class="form-control" name="admin_name" value="Administrador" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">E-mail</label>
                                <input type="email" class="form-control" name="admin_email" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Senha</label>
                                <input type="password" class="form-control" name="admin_password" required minlength="6">
                            </div>
                        </div>
                        
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Atenção:</strong> Anote bem essas credenciais, você precisará delas para acessar o sistema.
                        </div>
                        
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-rocket"></i> Instalar Sistema
                        </button>
                    </form>
                <?php endif; ?>

                <!-- Passo 4: Instalação Concluída -->
                <?php if ($step == 4): ?>
                    <div class="text-center">
                        <div class="mb-4">
                            <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                        </div>
                        
                        <h3 class="text-success">Instalação Concluída!</h3>
                        <p class="text-muted mb-4">O Sistema Meraki - Kanban foi instalado com sucesso.</p>
                        
                        <div class="alert alert-success text-start">
                            <h6><i class="fas fa-info-circle"></i> Próximos Passos:</h6>
                            <ol class="mb-0">
                                <li>Faça login no sistema com as credenciais do administrador</li>
                                <li>Configure as preferências do sistema</li>
                                <li>Crie outros usuários conforme necessário</li>
                                <li>Comece a usar o Kanban para gerenciar suas tarefas</li>
                            </ol>
                        </div>
                        
                        <div class="alert alert-warning text-start">
                            <h6><i class="fas fa-shield-alt"></i> Segurança:</h6>
                            <p class="mb-0">Por segurança, remova ou renomeie este arquivo de instalação (install.php) após o primeiro acesso ao sistema.</p>
                        </div>
                        
                        <a href="login.php" class="btn btn-primary btn-lg">
                            <i class="fas fa-sign-in-alt"></i> Acessar Sistema
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="text-center mt-3">
            <small class="text-white">
                <i class="fas fa-heart"></i> Sistema Meraki - Desenvolvido para Microlins Bauru
            </small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>