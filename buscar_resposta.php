<?php
require 'conexao.php';

$pergunta = $_GET['pergunta'] ?? '';
$pergunta = trim(mb_strtolower($pergunta, 'UTF-8'));

$stmt = $pdo->prepare("
    SELECT resposta FROM faq_meraki
    WHERE ativa = 1 AND (
        LOWER(pergunta) LIKE CONCAT('%', :pergunta, '%') OR
        LOWER(palavras_chave) LIKE CONCAT('%', :pergunta, '%')
    )
    LIMIT 1
");
$stmt->execute(['pergunta' => $pergunta]);

if ($resposta = $stmt->fetchColumn()) {
    echo $resposta;
} else {
    echo "Desculpe, não entendi sua pergunta. 😕";
}
