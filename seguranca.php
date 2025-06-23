<?php
// seguranca.php

/**
 * Inicia a sessão de forma segura, se ainda não estiver ativa.
 */
function iniciar_sessao_segura() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start([
            'cookie_httponly' => true, // Previne acesso ao cookie via JS
            'cookie_secure' => isset($_SERVER['HTTPS']), // Envia cookie apenas em HTTPS
            'cookie_samesite' => 'Lax' // Proteção contra CSRF
        ]);
    }
}

/**
 * Gera e retorna um campo de input oculto com o token CSRF.
 */
function gerar_csrf_input() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($_SESSION['csrf_token']) . '">';
}

/**
 * Valida o token CSRF de um formulário POST. Interrompe a execução se for inválido.
 */
function validar_csrf_post() {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
        // Em produção, você pode redirecionar para uma página de erro em vez de usar die().
        die('Erro de validação de segurança (CSRF). Por favor, tente novamente.');
    }
    // Opcional: remover o token após o uso para que não possa ser reenviado.
    unset($_SESSION['csrf_token']);
}

/**
 * Gera um token seguro para um aluno, combinando ID e um prazo de validade.
 * @param int $aluno_id O ID do aluno.
 * @return string O token gerado.
 */
function gerar_token_aluno($aluno_id) {
    // Token válido por 24 horas
    $vencimento = time() + 86400; 
    $dados = $aluno_id . '|' . $vencimento;
    $assinatura = hash_hmac('sha256', $dados, SECRET_KEY);
    return base64_encode($dados . '|' . $assinatura);
}

/**
 * Valida um token de aluno e retorna o ID se for válido e não estiver expirado.
 * @param string $token O token a ser validado.
 * @return int|false O ID do aluno ou false se o token for inválido/expirado.
 */
function validar_token_aluno($token) {
    $dados_decodificados = base64_decode($token, true);
    if ($dados_decodificados === false) {
        return false;
    }

    $partes = explode('|', $dados_decodificados);
    if (count($partes) !== 3) {
        return false;
    }

    list($aluno_id, $vencimento, $assinatura_recebida) = $partes;
    
    // Verifica se o token expirou
    if (time() > (int)$vencimento) {
        return false;
    }

    $dados_originais = $aluno_id . '|' . $vencimento;
    $assinatura_esperada = hash_hmac('sha256', $dados_originais, SECRET_KEY);

    if (hash_equals($assinatura_esperada, $assinatura_recebida)) {
        return (int)$aluno_id;
    }

    return false;
}
?>