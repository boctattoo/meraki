<!-- Modal Adicionar Tarefa -->
<div class="modal fade" id="modalAdicionarTarefa" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus"></i> Adicionar Nova Tarefa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formAdicionarTarefa">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Título *</label>
                        <input type="text" class="form-control" id="adicionar_titulo" required maxlength="255">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descrição</label>
                        <textarea class="form-control" id="adicionar_descricao" rows="3" maxlength="1000"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Data do Evento</label>
                        <input type="date" class="form-control" id="adicionar_data">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Prioridade</label>
                        <select class="form-select" id="adicionar_prioridade">
                            <option value="">Selecione</option>
                            <option value="Baixa">Baixa</option>
                            <option value="Média">Média</option>
                            <option value="Alta">Alta</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Etiqueta</label>
                        <input type="text" class="form-control" id="adicionar_etiqueta" maxlength="50" placeholder="Ex: Coordenação, Vendas, Administrativo">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Atribuir a</label>
                        <select class="form-select" id="adicionar_usuario_atribuido">
                            <option value="">Ninguém</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Adicionar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Tarefa -->
<div class="modal fade" id="modalEditarTarefa" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Editar Tarefa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditarTarefa">
                <input type="hidden" id="editar_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Título *</label>
                        <input type="text" class="form-control" id="editar_titulo" required maxlength="255">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descrição</label>
                        <textarea class="form-control" id="editar_descricao" rows="3" maxlength="1000"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Data do Evento</label>
                        <input type="date" class="form-control" id="editar_data">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Prioridade</label>
                        <select class="form-select" id="editar_prioridade">
                            <option value="">Selecione</option>
                            <option value="Baixa">Baixa</option>
                            <option value="Média">Média</option>
                            <option value="Alta">Alta</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Etiqueta</label>
                        <input type="text" class="form-control" id="editar_etiqueta" maxlength="50">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Atribuir a</label>
                        <select class="form-select" id="editar_usuario_atribuido">
                            <option value="">Ninguém</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tratativa -->
<div class="modal fade" id="modalTratativa" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-comments"></i> Registrar Tratativa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formTratativa">
                <input type="hidden" id="tratativa_tarefa_id">
                <input type="hidden" id="tratativa_status_anterior">
                <input type="hidden" id="tratativa_status_novo">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Alteração de Status:</strong> 
                        <span id="texto_mudanca_status"></span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descreva a tratativa realizada *</label>
                        <textarea class="form-control" id="tratativa_texto" rows="4" required 
                                placeholder="Descreva o que foi feito, motivo da mudança de status, próximos passos, etc."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="cancelarMudancaStatus()">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar e Mover
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Histórico de Tratativas -->
<div class="modal fade" id="modalHistoricoTratativas" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-history"></i> Histórico de Tratativas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <strong>Tarefa:</strong> <span id="historico_titulo_tarefa"></span>
                </div>
                <div id="lista_tratativas">
                    <!-- Tratativas serão carregadas aqui -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Confirmar Exclusão -->
<div class="modal fade" id="modalConfirmarExclusao" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle text-danger"></i> Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir esta tarefa?</p>
                <p class="text-muted">Esta ação não pode ser desfeita. Todo o histórico de tratativas também será removido.</p>
                <input type="hidden" id="excluir_tarefa_id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" onclick="confirmarExclusao()">
                    <i class="fas fa-trash"></i> Excluir
                </button>
            </div>
        </div>
    </div>
</div>