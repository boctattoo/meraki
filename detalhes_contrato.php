<?php
require 'conexao.php';

$contrato_id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("
    SELECT c.*, a.nome as nome_aluno, a.cpf_cnpj as cpf_aluno
    FROM contratos c 
    JOIN alunos a ON c.aluno_id = a.id 
    WHERE c.id = ?
");
$stmt->execute([$contrato_id]);
$contrato = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$contrato) {
    echo "Contrato não encontrado.";
    exit;
}
?>

<div class="row">
    <div class="col-md-6">
        <h6 class="text-primary">Dados do Aluno</h6>
        <p><strong>Nome:</strong> <?= htmlspecialchars($contrato['nome_aluno']) ?></p>
        <p><strong>CPF:</strong> <?= htmlspecialchars($contrato['cpf_aluno']) ?></p>
        
        <h6 class="text-primary mt-3">Dados do Pagador</h6>
        <p><strong>Nome:</strong> <?= htmlspecialchars($contrato['nome_pagador']) ?></p>
        <p><strong>CPF/CNPJ:</strong> <?= htmlspecialchars($contrato['cpf_cnpj_pagador']) ?></p>
        <p><strong>Telefone:</strong> <?= htmlspecialchars($contrato['telefone_pagador']) ?></p>
    </div>
    
    <div class="col-md-6">
        <h6 class="text-primary">Dados do Curso</h6>
        <p><strong>Cursos:</strong> <?= htmlspecialchars($contrato['cursos']) ?></p>
        <p><strong>Duração:</strong> <?= htmlspecialchars($contrato['duracao']) ?></p>
        <p><strong>Carga Horária:</strong> <?= htmlspecialchars($contrato['carga_horaria']) ?></p>
        <p><strong>Dias:</strong> <?= htmlspecialchars($contrato['dias_semana']) ?></p>
        <p><strong>Horário:</strong> <?= htmlspecialchars($contrato['horario']) ?></p>
        
        <h6 class="text-primary mt-3">Dados Financeiros</h6>
        <p><strong>Entrada:</strong> R$ <?= number_format($contrato['entrada'], 2, ',', '.') ?></p>
        <p><strong>Parcela Integral:</strong> R$ <?= number_format($contrato['parcela_integral'], 2, ',', '.') ?></p>
        <p><strong>Parcela c/ Desconto:</strong> R$ <?= number_format($contrato['parcela_com_desconto'], 2, ',', '.') ?></p>
        <p><strong>Quantidade de Meses:</strong> <?= $contrato['qtd_meses'] ?></p>
    </div>
</div>

<?php if (!empty($contrato['observacoes'])): ?>
<div class="row mt-3">
    <div class="col-12">
        <h6 class="text-primary">Observações</h6>
        <div class="alert alert-light border">
            <i class="fas fa-comment text-info me-2"></i>
            <?= nl2br(htmlspecialchars($contrato['observacoes'])) ?>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="row mt-3">
    <div class="col-12">
        <p class="text-muted small">
            <i class="fas fa-calendar me-1"></i>
            Contrato registrado em: <?= date('d/m/Y H:i', strtotime($contrato['data_criacao'])) ?>
        </p>
    </div>
</div>