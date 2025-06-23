<?php
/**
 * Funções Utilitárias do Sistema Meraki - Kanban
 * Microlins Bauru
 */

require_once 'config.php';

/**
 * Função para sanitizar dados de entrada
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Função para validar email
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Função para validar telefone brasileiro
 */
function isValidPhone($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    return preg_match('/^(?:\+55\s?)?(?:\(?[1-9][0-9]\)?\s?)?(?:9\s?)?[0-9]{4}[-\s]?[0-9]{4}$/', $phone);
}

/**
 * Função para formatar data brasileira
 */
function formatDateBR($date, $includeTime = false) {
    if (!$date) return '';
    
    $timestamp = is_string($date) ? strtotime($date) : $date;
    if (!$timestamp) return '';
    
    $format = $includeTime ? 'd/m/Y H:i' : 'd/m/Y';
    return date($format, $timestamp);
}

/**
 * Função para formatar data para MySQL
 */
function formatDateMySQL($date) {
    if (!$date) return null;
    
    // Se já está no formato MySQL
    if (preg_match('/^\d{4}-\d{2}-\d{2}/', $date)) {
        return $date;
    }
    
    // Converter formato brasileiro para MySQL
    $timestamp = strtotime(str_replace('/', '-', $date));
    return $timestamp ? date('Y-m-d', $timestamp) : null;
}

/**
 * Função para calcular idade
 */
function calculateAge($birthDate) {
    if (!$birthDate) return 0;
    
    $birth = new DateTime($birthDate);
    $today = new DateTime();
    return $birth->diff($today)->y;
}

/**
 * Função para gerar senha aleatória
 */
function generateRandomPassword($length = 8) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%';
    return substr(str_shuffle($chars), 0, $length);
}

/**
 * Função para hash de senha
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Função para verificar senha
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Função para gerar token CSRF
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
    return $_SESSION['csrf_token'];
}

/**
 * Função para verificar token CSRF
 */
function verifyCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
        return false;
    }
    
    // Verificar se o token expirou
    if (time() - $_SESSION['csrf_token_time'] > CSRF_TOKEN_EXPIRE) {
        unset($_SESSION['csrf_token'], $_SESSION['csrf_token_time']);
        return false;
    }
    
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Função para log do sistema
 */
function writeLog($message, $level = 'INFO', $file = 'system.log') {
    if (!isFeatureEnabled('log')) return;
    
    $logPath = LOG_PATH . $file;
    $timestamp = date('Y-m-d H:i:s');
    $userId = $_SESSION['usuario_id'] ?? 'GUEST';
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    
    $logEntry = "[{$timestamp}] [{$level}] [User: {$userId}] [IP: {$ip}] {$message}" . PHP_EOL;
    
    // Criar diretório se não existir
    if (!is_dir(LOG_PATH)) {
        mkdir(LOG_PATH, 0755, true);
    }
    
    file_put_contents($logPath, $logEntry, FILE_APPEND | LOCK_EX);
    
    // Rotacionar log se muito grande
    if (file_exists($logPath) && filesize($logPath) > LOG_MAX_SIZE) {
        rename($logPath, $logPath . '.' . date('Y-m-d-H-i-s'));
    }
}

/**
 * Função para enviar resposta JSON
 */
function sendJSONResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}

/**
 * Função para verificar se é requisição AJAX
 */
function isAjaxRequest() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Função para upload de arquivo
 */
function uploadFile($file, $allowedTypes = null, $destination = null) {
    if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Erro no upload do arquivo'];
    }
    
    // Verificar tamanho
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'error' => MENSAGENS['arquivo_muito_grande']];
    }
    
    // Verificar extensão
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedExts = $allowedTypes ?: ALLOWED_EXTENSIONS;
    
    if (!in_array($extension, $allowedExts)) {
        return ['success' => false, 'error' => MENSAGENS['extensao_nao_permitida']];
    }
    
    // Gerar nome único
    $filename = uniqid() . '.' . $extension;
    $uploadPath = ($destination ?: UPLOAD_PATH) . $filename;
    
    // Criar diretório se não existir
    if (!is_dir(dirname($uploadPath))) {
        mkdir(dirname($uploadPath), 0755, true);
    }
    
    // Mover arquivo
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        return ['success' => true, 'filename' => $filename, 'path' => $uploadPath];
    }
    
    return ['success' => false, 'error' => 'Erro ao salvar arquivo'];
}

/**
 * Função para verificar sessão
 */
function checkSession() {
    if (!isset($_SESSION['usuario_id'])) {
        if (isAjaxRequest()) {
            sendJSONResponse(['success' => false, 'error' => 'Sessão expirada'], 401);
        } else {
            header('Location: login.php');
            exit();
        }
    }
    
    // Verificar timeout da sessão
    if (isset($_SESSION['last_activity']) && 
        (time() - $_SESSION['last_activity']) > SESSAO_TIMEOUT) {
        session_destroy();
        if (isAjaxRequest()) {
            sendJSONResponse(['success' => false, 'error' => 'Sessão expirada'], 401);
        } else {
            header('Location: login.php?timeout=1');
            exit();
        }
    }
    
    $_SESSION['last_activity'] = time();
}

/**
 * Função para verificar permissão
 */
function checkPermission($permission) {
    if (!hasPermission($permission)) {
        if (isAjaxRequest()) {
            sendJSONResponse(['success' => false, 'error' => 'Permissão negada'], 403);
        } else {
            header('HTTP/1.1 403 Forbidden');
            include '403.php';
            exit();
        }
    }
}

/**
 * Função para gerar breadcrumb
 */
function generateBreadcrumb($items) {
    $html = '<nav aria-label="breadcrumb"><ol class="breadcrumb">';
    
    foreach ($items as $key => $item) {
        $isLast = ($key === array_key_last($items));
        
        if ($isLast) {
            $html .= '<li class="breadcrumb-item active" aria-current="page">' . 
                     htmlspecialchars($item['title']) . '</li>';
        } else {
            $html .= '<li class="breadcrumb-item">';
            if (isset($item['url'])) {
                $html .= '<a href="' . htmlspecialchars($item['url']) . '">' . 
                         htmlspecialchars($item['title']) . '</a>';
            } else {
                $html .= htmlspecialchars($item['title']);
            }
            $html .= '</li>';
        }
    }
    
    $html .= '</ol></nav>';
    return $html;
}

/**
 * Função para paginar resultados
 */
function paginate($totalItems, $currentPage = 1, $itemsPerPage = ITENS_POR_PAGINA) {
    $totalPages = ceil($totalItems / $itemsPerPage);
    $currentPage = max(1, min($currentPage, $totalPages));
    $offset = ($currentPage - 1) * $itemsPerPage;
    
    return [
        'total_items' => $totalItems,
        'total_pages' => $totalPages,
        'current_page' => $currentPage,
        'items_per_page' => $itemsPerPage,
        'offset' => $offset,
        'has_previous' => $currentPage > 1,
        'has_next' => $currentPage < $totalPages
    ];
}

/**
 * Função para truncar texto
 */
function truncateText($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    
    return substr($text, 0, $length) . $suffix;
}

/**
 * Função para formatar bytes
 */
function formatBytes($size, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    
    for ($i = 0; $size >= 1024 && $i < count($units) - 1; $i++) {
        $size /= 1024;
    }
    
    return round($size, $precision) . ' ' . $units[$i];
}

/**
 * Função para tempo relativo (há 2 horas, ontem, etc.)
 */
function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'agora mesmo';
    if ($time < 3600) return floor($time/60) . ' min atrás';
    if ($time < 86400) return floor($time/3600) . ' h atrás';
    if ($time < 2592000) return floor($time/86400) . ' dias atrás';
    if ($time < 31536000) return floor($time/2592000) . ' meses atrás';
    
    return floor($time/31536000) . ' anos atrás';
}

/**
 * Função para debug (apenas em modo desenvolvimento)
 */
function debug($data, $die = false) {
    if (!DEBUG_MODE) return;
    
    echo '<pre style="background: #f4f4f4; padding: 10px; margin: 10px; border: 1px solid #ddd;">';
    print_r($data);
    echo '</pre>';
    
    if ($die) die();
}

/**
 * Função para cache simples
 */
function getCache($key) {
    if (!isFeatureEnabled('cache')) return false;
    
    $cacheFile = CACHE_PATH . md5($key) . '.cache';
    
    if (!file_exists($cacheFile)) return false;
    
    $data = unserialize(file_get_contents($cacheFile));
    
    if (!$data || $data['expires'] < time()) {
        unlink($cacheFile);
        return false;
    }
    
    return $data['content'];
}

/**
 * Função para salvar cache
 */
function setCache($key, $content, $ttl = CACHE_TIME) {
    if (!isFeatureEnabled('cache')) return false;
    
    if (!is_dir(CACHE_PATH)) {
        mkdir(CACHE_PATH, 0755, true);
    }
    
    $cacheFile = CACHE_PATH . md5($key) . '.cache';
    $data = [
        'expires' => time() + $ttl,
        'content' => $content
    ];
    
    return file_put_contents($cacheFile, serialize($data)) !== false;
}

/**
 * Função para limpar cache
 */
function clearCache($pattern = '*') {
    if (!is_dir(CACHE_PATH)) return;
    
    $files = glob(CACHE_PATH . $pattern . '.cache');
    foreach ($files as $file) {
        unlink($file);
    }
}

/**
 * Função para notificação do sistema
 */
function addNotification($message, $type = 'info', $userId = null) {
    if (!isFeatureEnabled('notificacoes')) return;
    
    try {
        global $pdo;
        $sql = "INSERT INTO notificacoes (usuario_id, tipo, titulo, mensagem) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $userId ?: $_SESSION['usuario_id'],
            $type,
            ucfirst($type),
            $message
        ]);
    } catch (Exception $e) {
        writeLog("Erro ao adicionar notificação: " . $e->getMessage(), 'ERROR');
    }
}

/**
 * Função para backup do banco de dados
 */
function createDatabaseBackup() {
    if (!isFeatureEnabled('backup')) return false;
    
    try {
        $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        $filepath = BACKUP_PATH . $filename;
        
        if (!is_dir(BACKUP_PATH)) {
            mkdir(BACKUP_PATH, 0755, true);
        }
        
        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s %s > %s',
            DB_USER,
            DB_PASS,
            DB_HOST,
            DB_NAME,
            $filepath
        );
        
        exec($command, $output, $result);
        
        if ($result === 0) {
            writeLog("Backup criado com sucesso: {$filename}", 'INFO');
            return $filename;
        }
        
        return false;
        
    } catch (Exception $e) {
        writeLog("Erro ao criar backup: " . $e->getMessage(), 'ERROR');
        return false;
    }
}
?>