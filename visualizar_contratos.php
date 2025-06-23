<!-- Adicione esta seção no seu arquivo visualizar_contratos.php -->

<!-- Exemplo de como exibir as observações na lista de contratos -->
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <h5>Contratos Registrados</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Aluno</th>
                            <th>Curso</th>
                            <th>Valor</th>
                            <th>Data</th>
                            <th>Observações</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Buscar contratos incluindo observações
                        $stmt = $pdo->query("
                            SELECT c.*, a.nome as nome_aluno 
                            FROM contratos c 
                            JOIN alunos a ON c.aluno_id = a.id 
                            ORDER BY c.data_criacao DESC
                        ");
                        
                        while ($contrato = $stmt->fetch(PDO::FETCH_ASSOC)): 
                        ?>
                        <tr>
                            <td><?= $contrato['id'] ?></td>
                            <td><?= htmlspecialchars($contrato['nome_aluno']) ?></td>
                            <td><?= htmlspecialchars($contrato['cursos']) ?></td>
                            <td>R$ <?= number_format($contrato['parcela_integral'], 2, ',', '.') ?></td>
                            <td><?= date('d/m/Y', strtotime($contrato['data_criacao'])) ?></td>
                            <td>
                                <?php if (!empty($contrato['observacoes'])): ?>
                                    <button type="button" class="btn btn-sm btn-outline-info" 
                                            data-bs-toggle="tooltip" 
                                            title="<?= htmlspecialchars($contrato['observacoes']) ?>">
                                        <i class="fas fa-comment"></i>
                                    </button>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="verDetalhes(<?= $contrato['id'] ?>)">
                                    Ver Detalhes
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal para exibir detalhes completos do contrato -->
<div class="modal fade" id="modalDetalhes" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalhes do Contrato</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="conteudoDetalhes">
                <!-- Conteúdo carregado via AJAX -->
            </div>
        </div>
    </div>
</div>

<script>
// Inicializar tooltips
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
});

function verDetalhes(contratoId) {
    // Carregar detalhes via AJAX
    fetch('detalhes_contrato.php?id=' + contratoId)
        .then(response => response.text())
        .then(data => {
            document.getElementById('conteudoDetalhes').innerHTML = data;
            new bootstrap.Modal(document.getElementById('modalDetalhes')).show();
        });
}
</script>