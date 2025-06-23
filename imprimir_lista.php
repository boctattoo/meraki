<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}
require 'conexao.php';

$turma_id = filter_input(INPUT_GET, 'turma_id', FILTER_VALIDATE_INT);
$data_aula = filter_input(INPUT_GET, 'data', FILTER_SANITIZE_STRING);

if (!$turma_id || !$data_aula) {
    die("Turma ou data não especificadas.");
}

$turma_info = $pdo->prepare("SELECT t.nome, i.nome as instrutor FROM turmas t LEFT JOIN instrutores i ON t.instrutor_id = i.id WHERE t.id = ?");
$turma_info->execute([$turma_id]);
$turma = $turma_info->fetch();

$alunos_stmt = $pdo->prepare("SELECT a.id, a.nome, p.presente FROM alunos a JOIN alunos_turmas at ON a.id = at.aluno_id LEFT JOIN presencas p ON a.id = p.aluno_id AND p.turma_id = ? AND p.data = ? WHERE at.turma_id = ? AND at.ativo = 1 AND a.status = 'Ativo' ORDER BY a.nome");
$alunos_stmt->execute([$turma_id, $data_aula, $turma_id]);
$alunos = $alunos_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Lista de Presença para Impressão</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @media print {
            body { -webkit-print-color-adjust: exact; }
            .no-print { display: none !important; }
        }
        .signature-line {
            border-bottom: 1px solid #333;
            display: inline-block;
            width: 100%;
            height: 25px;
        }
    </style>
</head>
<body>
    <div class="container my-4">
        <div class="text-center mb-4 no-print">
            <button class="btn btn-primary" onclick="window.print()"><i class="fas fa-print me-2"></i>Imprimir Lista</button>
            <a href="javascript:window.close()" class="btn btn-secondary">Fechar</a>
        </div>
        
        <div class="text-center mb-4">
            <h4>MICROLINS BAURU - LISTA DE PRESENÇA</h4>
            <p class="mb-0"><strong>Turma:</strong> <?php echo htmlspecialchars($turma['nome']); ?></p>
            <p class="mb-0"><strong>Instrutor:</strong> <?php echo htmlspecialchars($turma['instrutor']); ?></p>
            <p class="mb-0"><strong>Data:</strong> <?php echo date('d/m/Y', strtotime($data_aula)); ?></p>
        </div>

        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th style="width: 40%;">Nome do Aluno</th>
                    <th style="width: 10%;" class="text-center">Status</th>
                    <th>Assinatura</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($alunos as $aluno): ?>
                    <tr>
                        <td class="align-middle"><?php echo htmlspecialchars($aluno['nome']); ?></td>
                        <td class="text-center align-middle">
                            <?php if ($aluno['presente'] === '1'): echo 'Presente'; ?>
                            <?php elseif ($aluno['presente'] === '0'): echo 'Ausente'; ?>
                            <?php else: echo 'N/A'; endif; ?>
                        </td>
                        <td>
                            <?php if ($aluno['presente'] === '1'): ?>
                                <div class="signature-line"></div>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
         <div class="mt-5 text-center">
            <div style="display: inline-block; width: 45%; border-top: 1px solid #000; padding-top: 5px;">Assinatura do Instrutor</div>
        </div>
    </div>
</body>
</html>
