<div class="modal fade" id="modalAdicionarTarefa" tabindex="-1" aria-labelledby="labelAdicionarTarefa" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formAdicionarTarefa" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="labelAdicionarTarefa">Adicionar Nova Tarefa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="adicionar_titulo" class="form-label">Título da Tarefa</label>
                    <input type="text" class="form-control" id="adicionar_titulo" name="titulo" required maxlength="100">
                </div>
                <div class="mb-3">
                    <label for="adicionar_descricao" class="form-label">Descrição</label>
                    <textarea class="form-control" id="adicionar_descricao" name="descricao" rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <label for="adicionar_data" class="form-label">Data do Evento (Opcional)</label>
                    <input type="date" class="form-control" id="adicionar_data" name="data_evento">
                </div>
                <div class="mb-3">
                    <label for="adicionar_prioridade" class="form-label">Prioridade</label>
                    <select class="form-select" id="adicionar_prioridade" name="prioridade">
                        <option value="">Sem prioridade</option>
                        <option value="baixa">Baixa</option>
                        <option value="media">Média</option>
                        <option value="alta">Alta</option>
                        <option value="urgente">Urgente</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="adicionar_etiqueta" class="form-label">Etiqueta (Ex: Urgente, Bug)</label>
                    <input type="text" class="form-control" id="adicionar_etiqueta" name="etiqueta" maxlength="50">
                </div>
                <div class="mb-3">
                    <label for="adicionar_usuario_atribuido" class="form-label">Atribuir a</label>
                    <select class="form-select" id="adicionar_usuario_atribuido" name="usuario_atribuido">
                        <option value="">Ninguém</option>
                        </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Adicionar Tarefa</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalEditarTarefa" tabindex="-1" aria-labelledby="labelEditarTarefa" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formEditarTarefa" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="labelEditarTarefa">Editar Tarefa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editar_id" name="id">
                <div class="mb-3">
                    <label for="editar_titulo" class="form-label">Título</label>
                    <input type="text" class="form-control" id="editar_titulo" name="titulo" required maxlength="100">
                </div>
                <div class="mb-3">
                    <label for="editar_descricao" class="form-label">Descrição</label>
                    <textarea class="form-control" id="editar_descricao" name="descricao" rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <label for="editar_data" class="form-label">Data</label>
                    <input type="date" class="form-control" id="editar_data" name="data_evento">
                </div>
                <div class="mb-3">
                    <label for="editar_prioridade" class="form-label">Prioridade</label>
                    <select class="form-select" id="editar_prioridade" name="prioridade">
                        <option value="">Sem prioridade</option>
                        <option value="baixa">Baixa</option>
                        <option value="media">Média</option>
                        <option value="alta">Alta</option>
                        <option value="urgente">Urgente</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="editar_etiqueta" class="form-label">Etiqueta</label>
                    <input type="text" class="form-control" id="editar_etiqueta" name="etiqueta" maxlength="50">
                </div>
                <div class="mb-3">
                    <label for="editar_usuario_atribuido" class="form-label">Atribuir a</label>
                    <select class="form-select" id="editar_usuario_atribuido" name="usuario_atribuido">
                        <option value="">Ninguém</option>
                        </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalTratativa" tabindex="-1" aria-labelledby="labelTratativa" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formTratativa" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="labelTratativa">Registrar Tratativa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="tratativa_tarefa_id">
                <input type="hidden" id="tratativa_status_anterior">
                <input type="hidden" id="tratativa_status_novo">
                <div class="mb-3">
                    <label for="tratativa_texto" class="form-label">Descreva a tratativa:</label>
                    <textarea class="form-control" id="tratativa_texto" rows="4" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Confirmar e Mover</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalExcluir" tabindex="-1" aria-labelledby="labelExcluir" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formExcluirTarefa" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="labelExcluir">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir esta tarefa? Esta ação não pode ser desfeita.</p>
                <input type="hidden" id="excluir_id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-danger">Excluir</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalHistoricoTratativas" tabindex="-1" aria-labelledby="labelHistoricoTratativas" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="labelHistoricoTratativas">Histórico de Tratativas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body" id="modalTratativasBody">
                <p class="text-center text-muted">Carregando histórico...</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>