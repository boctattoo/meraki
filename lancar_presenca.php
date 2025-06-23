<?php
require_once 'config.php';
require_once 'seguranca.php';
require_once 'conexao.php';

iniciar_sessao_segura();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

$turma_id = filter_input(INPUT_GET, 'turma_id', FILTER_VALIDATE_INT);
$data_aula = filter_input(INPUT_GET, 'data', FILTER_SANITIZE_STRING) ?? date('Y-m-d');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validar_csrf_post(); // Validação CSRF

    $turma_id_post = filter_input(INPUT_POST, 'turma_id', FILTER_VALIDATE_INT);
    $data_post = filter_input(INPUT_POST, 'data_aula', FILTER_SANITIZE_STRING);
    $todos_alunos_ids = $_POST['aluno_id'] ?? [];

    if ($turma_id_post && $data_post && !empty($todos_alunos_ids)) {
        $pdo->beginTransaction();
        try {
            $sql = "INSERT INTO presencas (aluno_id, turma_id, data, presente) VALUES (:aluno_id, :turma_id, :data, :presente) ON DUPLICATE KEY UPDATE presente = VALUES(presente)";
            $stmt = $pdo->prepare($sql);

            foreach ($todos_alunos_ids as $aluno_id) {
                $presente = isset($_POST['aluno'][$aluno_id]['presente']) ? 1 : 0;
                $stmt->execute([':aluno_id' => $aluno_id, ':turma_id' => $turma_id_post, ':data' => $data_post, ':presente' => $presente]);
            }
            $pdo->commit();
            $_SESSION['mensagem_sucesso'] = "Presença salva com sucesso!";
        } catch (Exception $e) {
            $pdo->rollBack();
            $_SESSION['mensagem_erro'] = "Erro ao salvar: " . $e->getMessage();
        }
    }
    header("Location: lancar_presenca.php?turma_id=$turma_id_post&data=$data_post");
    exit();
}

$turmas = $pdo->query("SELECT id, nome FROM turmas WHERE status = 'ativa' ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
$alunos = [];
$dados_presenca = [];
$faltosos_count = 0;

if ($turma_id) {
    $alunos_stmt = $pdo->prepare("SELECT a.id, a.nome FROM alunos a JOIN alunos_turmas at ON a.id = at.aluno_id WHERE at.turma_id = ? AND at.ativo = 1 AND a.status = 'Ativo' ORDER BY a.nome");
    $alunos_stmt->execute([$turma_id]);
    $alunos = $alunos_stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($alunos) {
        $stmt_presencas = $pdo->prepare("SELECT aluno_id, presente FROM presencas WHERE turma_id = ? AND data = ?");
        $stmt_presencas->execute([$turma_id, $data_aula]);
        $dados_presenca = $stmt_presencas->fetchAll(PDO::FETCH_KEY_PAIR);
        
        $stmt_faltas = $pdo->prepare("SELECT COUNT(*) FROM presencas WHERE turma_id = ? AND data = ? AND presente = 0");
        $stmt_faltas->execute([$turma_id, $data_aula]);
        $faltosos_count = $stmt_faltas->fetchColumn();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Lançamento de Presença</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include 'nav.php'; ?>
    <div class="container-fluid mt-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white"><h4 class="mb-0"><i class="fas fa-calendar-check me-2"></i>Lançamento de Aula</h4></div>
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-5"><label for="turma_id" class="form-label">Turma</label><select name="turma_id" id="turma_id" class="form-select" onchange="this.form.submit()"><option value="">Selecione...</option><?php foreach($turmas as $t):?><option value="<?php echo $t['id']?>" <?php if($t['id']==$turma_id) echo 'selected';?>><?php echo htmlspecialchars($t['nome'])?></option><?php endforeach;?></select></div>
                    <div class="col-md-3"><label for="data" class="form-label">Data da Aula</label><input type="date" id="data" name="data" class="form-control" value="<?php echo htmlspecialchars($data_aula); ?>" onchange="this.form.submit()"></div>
                </form>
            </div>
            <?php if($faltosos_count > 0): ?>
            <div class="card-footer bg-light"><a href="notificar_faltas.php?data=<?php echo htmlspecialchars($data_aula); ?>&turma_id=<?php echo htmlspecialchars($turma_id); ?>" class="btn btn-warning"><i class="fab fa-whatsapp"></i> Notificar <?php echo $faltosos_count; ?> Faltoso(s)</a></div>
            <?php endif; ?>
        </div>

        <?php if ($turma_id && !empty($alunos)): ?>
        <form method="POST" class="mt-4">
            <?php gerar_csrf_input(); // Proteção CSRF ?>
            <input type="hidden" name="turma_id" value="<?php echo htmlspecialchars($turma_id); ?>">
            <input type="hidden" name="data_aula" value="<?php echo htmlspecialchars($data_aula); ?>">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Alunos da Turma</h5>
                    <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Salvar Presença</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Aluno</th>
                                <th class="text-center">Presente</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($alunos as $aluno):
                            // Por padrão, se não houver registro, o aluno é considerado presente.
                            $presente = $dados_presenca[$aluno['id']] ?? 1;
                        ?>
                            <tr>
                                <td>
                                    <?php echo htmlspecialchars($aluno['nome']); ?>
                                    <input type="hidden" name="aluno_id[]" value="<?php echo $aluno['id']; ?>">
                                </td>
                                <td class="text-center">
                                    <div class="form-check form-switch d-inline-block p-0">
                                        <input class="form-check-input mx-auto" type="checkbox" role="switch" name="aluno[<?php echo $aluno['id']; ?>][presente]" value="1" <?php if($presente) echo 'checked';?> style="height: 1.5em; width: 3em;">
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </form>
        <?php elseif ($turma_id): ?>
            <div class="alert alert-info mt-4">Nenhum aluno ativo encontrado para esta turma.</div>
        <?php endif; ?>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>