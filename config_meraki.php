<?php
// config.php

// --- Configurações do Banco de Dados ---
define('DB_HOST', 'localhost');
define('DB_NAME', 'microl68_meraki'); // Nome do seu banco de dados
define('DB_USER', 'root');      // Seu usuário do banco de dados
define('DB_PASS', '');          // Sua senha do banco de dados
define('DB_CHARSET', 'utf8mb4');

// --- Configurações de Segurança ---
// Chave secreta para gerar tokens. MUDE ESTE VALOR para uma string longa e aleatória.
// Você pode usar `echo bin2hex(random_bytes(32));` para gerar uma.
define('SECRET_KEY', '4e128436e963b591b7d39d919493a746a512b627993a4f494a28243a7a9e1e9b');

// --- Configurações Gerais da Aplicação ---
// URL base do sistema. Ex: http://localhost/meraki ou https://seu-dominio.com
// Deixe em branco para detecção automática, mas definir explicitamente é mais seguro.
define('BASE_URL', ''); 
?>