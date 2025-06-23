<?php
require_once 'config_meraki.php';
require_once 'seguranca.php';
require_once 'conexao.php';

iniciar_sessao_segura();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

// --- Parâmetros ---
$data_selecionada = filter_input(INPUT_GET, 'data', FILTER_SANITIZE_STRING) ?? date('Y-m-d');
$turma_id_selecionada = filter_input(INPUT_GET, 'turma_id', FILTER_VALIDATE_INT);

// --- Busca de Dados ---
$stmt_turmas = $pdo->prepare("
    SELECT t.id as turma_id, t.nome as nome_turma, COUNT(p.aluno_id) as total_faltas
    FROM presencas p JOIN turmas t ON p.turma_id = t.id
    WHERE p.data = :data AND p.presente = 0 GROUP BY t.id, t.nome ORDER BY t.nome
");
$stmt_turmas->execute([':data' => $data_selecionada]);
$turmas_com_faltas = $stmt_turmas->fetchAll(PDO::FETCH_ASSOC);

$faltosos_detalhes = [];
if ($turma_id_selecionada) {
   $stmt_detalhes = $pdo->prepare("
    SELECT 
        p.aluno_id, 
        COALESCE(a.nome, 'Aluno Excluído') as nome_aluno, 
        COALESCE(a.telefone, 'N/A') as telefone 
    FROM presencas p
    LEFT JOIN alunos a ON p.aluno_id = a.id
    WHERE p.turma_id = :turma_id AND p.data = :data AND p.presente = 0 
    ORDER BY a.nome
");
    $stmt_detalhes->execute([':turma_id' => $turma_id_selecionada, ':data' => $data_selecionada]);
    $faltosos_detalhes = $stmt_detalhes->fetchAll(PDO::FETCH_ASSOC);
}

// --- URL base para o link de reposição ---
$base_url = BASE_URL;
if(empty($base_url)){
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $domain_name = $_SERVER['HTTP_HOST'];
    $path = dirname($_SERVER['REQUEST_URI']);
    $base_url = rtrim($protocol . $domain_name . $path, '/');
}
$reposicao_base_url = $base_url . '/agendar_reposicao.php';

// --- Modelo da Mensagem WhatsApp ---
$data_aula_formatada = date('d/m/Y', strtotime($data_selecionada));
$modelo_mensagem = "Olá {NOME_ALUNO}, notamos sua ausência na aula de hoje ({DATA_AULA}). Se desejar, pode agendar uma aula de reposição aqui: {LINK_REPOSICAO}";

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificar Faltas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <?php include 'nav.php'; ?>
    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fab fa-whatsapp me-2"></i>Notificar Alunos Ausentes</h4>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end mb-4">
                    <div class="col-md-4"><label for="data" class="form-label fw-bold">Mostrar faltosos do dia:</label><input type="date" name="data" id="data" class="form-control" value="<?php echo htmlspecialchars($data_selecionada); ?>"></div>
                    <div class="col-md-3"><button type="submit" class="btn btn-primary w-100"><i class="fas fa-search me-1"></i>Buscar</button></div>
                </form>

                <?php if (empty($turmas_com_faltas)): ?>
                    <div class="alert alert-success text-center"><i class="fas fa-check-circle fa-2x mb-2"></i><br>Nenhuma falta registada na data selecionada.</div>
                <?php else: ?>
                    <h5 class="mb-3">Turmas com Faltas em <?php echo date('d/m/Y', strtotime($data_selecionada)); ?></h5>
                    <div class="list-group mb-4">
                        <?php foreach($turmas_com_faltas as $turma): ?>
                            <a href="?data=<?php echo htmlspecialchars($data_selecionada); ?>&turma_id=<?php echo $turma['turma_id']; ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?php echo ($turma_id_selecionada == $turma['turma_id']) ? 'active' : ''; ?>"><?php echo htmlspecialchars($turma['nome_turma']); ?><span class="badge bg-danger rounded-pill"><?php echo $turma['total_faltas']; ?> falta(s)</span></a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($faltosos_detalhes)): ?>
                    <hr><h5 class="mt-4 mb-3">Selecionar Alunos para Notificar</h5>
                    <form id="form-notificacao">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light"><tr><th style="width: 50px;"><input class="form-check-input" type="checkbox" id="selecionar-todos"></th><th>Aluno</th><th>Telefone</th></tr></thead>
                                <tbody>
                                    <?php foreach ($faltosos_detalhes as $aluno): ?>
                                    <tr>
                                        <td><input class="form-check-input aluno-checkbox" type="checkbox" 
                                                   data-nome="<?php echo htmlspecialchars($aluno['nome_aluno']); ?>" 
                                                   data-telefone="<?php echo htmlspecialchars($aluno['telefone']); ?>" 
                                                   data-token="<?php echo htmlspecialchars(gerar_token_aluno($aluno['aluno_id'])); // Geração do Token Seguro ?>">
                                        </td>
                                        <td><?php echo htmlspecialchars($aluno['nome_aluno']); ?></td>
                                        <td><?php echo htmlspecialchars($aluno['telefone']); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3 d-flex justify-content-end"><button type="button" class="btn btn-success" id="btn-gerar-links"><i class="fas fa-paper-plane me-2"></i>Gerar Links de Notificação</button></div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="modal fade" id="linksModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Links de Notificação Gerados</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body" id="modal-body-links"></div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button></div>
            </div>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnGerarLinks = document.getElementById('btn-gerar-links');
    const selecionarTodos = document.getElementById('selecionar-todos');
    const checkboxesAlunos = document.querySelectorAll('.aluno-checkbox');
    const linksModal = new bootstrap.Modal(document.getElementById('linksModal'));
    const modalBody = document.getElementById('modal-body-links');

    selecionarTodos?.addEventListener('change', function() { checkboxesAlunos.forEach(c => c.checked = this.checked); });

    btnGerarLinks?.addEventListener('click', function() {
        const selecionados = document.querySelectorAll('.aluno-checkbox:checked');
        if (selecionados.length === 0) { alert('Selecione pelo menos um aluno.'); return; }

        modalBody.innerHTML = ''; 
        const reposicaoBaseUrl = '<?php echo $reposicao_base_url; ?>';
        const modeloMensagem = '<?php echo addslashes($modelo_mensagem); ?>';
        const dataAula = '<?php echo addslashes($data_aula_formatada); ?>';

        selecionados.forEach(checkbox => {
            const nome = checkbox.dataset.nome;
            const telefone = checkbox.dataset.telefone.replace(/\D/g, '');
            const token = checkbox.dataset.token;

            if (telefone && token) {
                const linkReposicao = `${reposicaoBaseUrl}?token=${token}`;
                
                let mensagem = modeloMensagem.replace('{NOME_ALUNO}', nome.split(' ')[0]);
                mensagem = mensagem.replace('{DATA_AULA}', dataAula);
                mensagem = mensagem.replace('{LINK_REPOSICAO}', linkReposicao);
                
                const whatsappUrl = `https://wa.me/55${telefone}?text=${encodeURIComponent(mensagem)}`;

                const linkGroup = document.createElement('div');
                linkGroup.className = 'input-group mb-3';
                linkGroup.innerHTML = `<span class="input-group-text">${nome}</span><input type="text" class="form-control" value="${whatsappUrl}" readonly><a href="${whatsappUrl}" target="_blank" class="btn btn-outline-success"><i class="fab fa-whatsapp"></i></a><button class="btn btn-outline-secondary btn-copy" type="button" title="Copiar Link"><i class="fas fa-copy"></i></button>`;
                modalBody.appendChild(linkGroup);
            }
        });
        
        linksModal.show();
    });
    
    modalBody.addEventListener('click', function(e) {
        const target = e.target.closest('.btn-copy');
        if (target) {
            const input = target.parentElement.querySelector('input');
            input.select();
            document.execCommand('copy');
            target.innerHTML = '<i class="fas fa-check text-success"></i>';
            setTimeout(() => { target.innerHTML = '<i class="fas fa-copy"></i>'; }, 2000);
        }
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>