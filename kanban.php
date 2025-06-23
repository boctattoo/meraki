<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}
require 'conexao.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kanban | Meraki</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/dragula/3.7.2/dragula.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .kanban-column {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            min-height: 500px;
            border: 1px solid #dee2e6;
        }
        
        .kanban-title {
            text-align: center;
            color: #495057;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #dee2e6;
        }
        
        .kanban-card {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 10px;
            cursor: move;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .kanban-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            transform: translateY(-2px);
        }
        
        .kanban-actions {
            margin-top: 10px;
            display: flex;
            gap: 5px;
            justify-content: flex-end;
        }
        
        .kanban-actions button {
            border: none;
            background: none;
            padding: 5px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s;
            color: #6c757d;
        }
        
        .kanban-actions button:hover {
            background-color: #f8f9fa;
        }
        
        .prioridade-alta { color: #dc3545; font-weight: bold; }
        .prioridade-media { color: #fd7e14; font-weight: bold; }
        .prioridade-baixa { color: #28a745; font-weight: bold; }
        
        .etiqueta {
            background-color: #e9ecef;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
            color: #495057;
            display: inline-block;
            margin-top: 5px;
        }
        
        .tratativa-item {
            border-left: 3px solid #007bff;
            padding-left: 15px;
            margin-bottom: 15px;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
        }
        
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
        }
        
        .loading-spinner {
            display: none;
            text-align: center;
            padding: 20px;
        }
        
        .kanban-tarefas {
            min-height: 400px;
        }
        
        .gu-mirror {
            opacity: 0.8;
            transform: rotate(5deg);
        }
        
        .gu-hide {
            display: none !important;
        }
        
        .gu-unselectable {
            -webkit-user-select: none !important;
            -moz-user-select: none !important;
            -ms-user-select: none !important;
            user-select: none !important;
        }
        
        .gu-transit {
            opacity: 0.2;
        }

        .status-badge {
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
        }
        
        .status-afazer { background-color: #6c757d; color: white; }
        .status-progresso { background-color: #fd7e14; color: white; }
        .status-concluido { background-color: #28a745; color: white; }
    </style>
</head>
<body>

<?php 
if (file_exists('nav.php')) {
    include 'nav.php';
} else {
    echo '<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container">
                <a class="navbar-brand" href="dashboard.php">
                    <i class="fas fa-graduation-cap"></i> Sistema Meraki
                </a>
                <div class="navbar-nav ms-auto">
                    <a class="nav-link" href="dashboard.php">Dashboard</a>
                    <a class="nav-link active" href="kanban.php">Kanban</a>
                    <a class="nav-link" href="logout.php">Sair</a>
                </div>
            </div>
          </nav>';
}
?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-tasks"></i> Painel de Tarefas (Kanban)</h2>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary" onclick="carregarTarefas()">
                <i class="fas fa-sync-alt"></i> Atualizar
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAdicionarTarefa">
                <i class="fas fa-plus"></i> Adicionar Tarefa
            </button>
        </div>
    </div>

    <div class="row filtros-kanban mb-3">
        <div class="col-md-8 mb-2">
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input type="text" id="filtroBusca" class="form-control" 
                       placeholder="Buscar tarefa por título, descrição ou usuário..." 
                       onkeyup="filtrarTarefas()">
                <button class="btn btn-outline-secondary" onclick="limparFiltro()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="col-md-4 mb-2">
            <select id="filtroPrioridade" class="form-select" onchange="filtrarTarefas()">
                <option value="">Todas as prioridades</option>
                <option value="Alta">Alta</option>
                <option value="Média">Média</option>
                <option value="Baixa">Baixa</option>
            </select>
        </div>
    </div>

    <div class="loading-spinner" id="loadingSpinner">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Carregando...</span>
        </div>
        <p class="mt-2">Carregando tarefas...</p>
    </div>

    <div class="row" id="quadro-kanban">
        <div class="col-md-4">
            <div class="kanban-column" id="afazer">
                <h4 class="kanban-title">
                    <i class="fas fa-clipboard-list"></i> A Fazer
                    <span class="badge bg-secondary ms-2" id="count-afazer">0</span>
                </h4>
                <div class="kanban-tarefas"></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kanban-column" id="progresso">
                <h4 class="kanban-title">
                    <i class="fas fa-hourglass-half"></i> Em Progresso
                    <span class="badge bg-warning ms-2" id="count-progresso">0</span>
                </h4>
                <div class="kanban-tarefas"></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="kanban-column" id="concluido">
                <h4 class="kanban-title">
                    <i class="fas fa-check-circle"></i> Concluído
                    <span class="badge bg-success ms-2" id="count-concluido">0</span>
                </h4>
                <div class="kanban-tarefas"></div>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div class="toast-container" id="toastContainer"></div>

<!-- Incluir modais -->
<?php include 'modais_kanban.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dragula/3.7.2/dragula.min.js"></script>

<script>
class KanbanManager {
    constructor() {
        this.todasAsTarefas = {
            afazer: [],
            progresso: [],
            concluido: []
        };
        this.todosOsUsuarios = [];
        this.dragula = null;
        this.tarefaSendoMovida = null;
        this.init();
    }

    async init() {
        try {
            this.mostrarLoading(true);
            await this.carregarUsuarios();
            await this.carregarTarefas();
            this.inicializarDragula();
            this.configurarEventListeners();
            this.mostrarLoading(false);
        } catch (error) {
            console.error('Erro ao inicializar Kanban:', error);
            this.showToast('Erro ao carregar o sistema', 'danger');
            this.mostrarLoading(false);
        }
    }

    mostrarLoading(show) {
        const spinner = document.getElementById('loadingSpinner');
        const kanban = document.getElementById('quadro-kanban');
        if (show) {
            spinner.style.display = 'block';
            kanban.style.opacity = '0.3';
        } else {
            spinner.style.display = 'none';
            kanban.style.opacity = '1';
        }
    }

    inicializarDragula() {
        if (this.dragula) {
            this.dragula.destroy();
        }

        this.dragula = dragula([
            document.querySelector('#afazer .kanban-tarefas'),
            document.querySelector('#progresso .kanban-tarefas'),
            document.querySelector('#concluido .kanban-tarefas')
        ], {
            moves: (el, container, handle) => {
                return !handle.classList.contains('kanban-actions') && 
                       !handle.closest('.kanban-actions');
            }
        });

        this.dragula.on('drop', (el, target, source) => {
            const tarefaId = el.dataset.id;
            const novoStatus = target.closest('.kanban-column').id;
            const statusAnterior = source.closest('.kanban-column').id;

            if (novoStatus !== statusAnterior) {
                this.tarefaSendoMovida = {
                    elemento: el,
                    tarefaId: tarefaId,
                    statusAnterior: statusAnterior,
                    novoStatus: novoStatus,
                    containerOrigem: source
                };
                this.abrirModalTratativa(tarefaId, statusAnterior, novoStatus);
            }
        });
    }

    configurarEventListeners() {
        // Formulário de adicionar tarefa
        document.getElementById('formAdicionarTarefa').addEventListener('submit', (e) => {
            this.adicionarTarefa(e);
        });

        // Formulário de editar tarefa
        document.getElementById('formEditarTarefa').addEventListener('submit', (e) => {
            this.editarTarefa(e);
        });

        // Formulário de tratativa
        document.getElementById('formTratativa').addEventListener('submit', (e) => {
            this.salvarTratativa(e);
        });

        // Event delegation para botões das tarefas
        document.querySelectorAll('.kanban-tarefas').forEach(container => {
            container.addEventListener('click', (event) => {
                event.stopPropagation();
                const target = event.target.closest('button');
                if (!target) return;

                const taskId = target.dataset.id;
                
                if (target.classList.contains('btn-ver-tratativas')) {
                    this.abrirModalTratativasHistorico(taskId);
                } else if (target.classList.contains('btn-editar')) {
                    const taskData = JSON.parse(target.dataset.task);
                    this.abrirModalEditar(taskData);
                } else if (target.classList.contains('btn-excluir')) {
                    this.abrirModalExcluir(taskId);
                }
            });
        });
    }

    async carregarUsuarios() {
        try {
            const resposta = await fetch('carregar_usuarios.php');
            const dados = await resposta.json();

            if (dados.sucesso) {
                this.todosOsUsuarios = dados.usuarios;
                this.popularDropdownUsuarios();
            } else {
                console.error('Erro ao carregar usuários:', dados.erro);
                this.showToast('Erro ao carregar lista de usuários.', 'warning');
            }
        } catch (error) {
            console.error('Erro de conexão ao carregar usuários:', error);
            this.showToast('Erro de conexão ao carregar lista de usuários.', 'warning');
        }
    }

    popularDropdownUsuarios() {
        const selects = ['adicionar_usuario_atribuido', 'editar_usuario_atribuido'];
        
        selects.forEach(selectId => {
            const select = document.getElementById(selectId);
            if (select) {
                select.innerHTML = '<option value="">Ninguém</option>';
                this.todosOsUsuarios.forEach(usuario => {
                    const option = document.createElement('option');
                    option.value = usuario.id;
                    option.textContent = usuario.nome;
                    select.appendChild(option);
                });
            }
        });
    }

    async carregarTarefas() {
        try {
            const resposta = await fetch('carregar_tarefas.php');
            const dados = await resposta.json();

            if (!dados.sucesso) {
                this.showToast(dados.erro, 'danger');
                return;
            }

            this.todasAsTarefas = dados.tarefas;
            this.renderizarTarefas(this.todasAsTarefas);
            this.atualizarContadores();

        } catch (error) {
            console.error('Erro ao carregar tarefas:', error);
            this.showToast('Não foi possível carregar as tarefas. Verifique a conexão.', 'danger');
        }
    }

    renderizarTarefas(tarefasObj) {
        ['afazer', 'progresso', 'concluido'].forEach(status => {
            const container = document.querySelector(`#${status} .kanban-tarefas`);
            container.innerHTML = '';
            
            tarefasObj[status].forEach(tarefa => {
                const div = document.createElement('div');
                div.className = 'kanban-card';
                div.dataset.id = tarefa.id;
                div.innerHTML = this.criarCardTarefa(tarefa);
                container.appendChild(div);
            });
        });
    }

    criarCardTarefa(tarefa) {
        const prioridadeClass = tarefa.prioridade ? 
            `prioridade-${tarefa.prioridade.toLowerCase().replace('é', 'e')}` : '';
        const dataEvento = tarefa.data_evento ? 
            `<div class="small text-muted mt-1"><i class="fas fa-calendar"></i> ${this.formatarData(tarefa.data_evento)}</div>` : '';
        const prioridade = tarefa.prioridade ? 
            `<div class="small ${prioridadeClass} mt-1"><i class="fas fa-exclamation-circle"></i> ${tarefa.prioridade}</div>` : '';
        const etiqueta = tarefa.etiqueta ? 
            `<div class="etiqueta mt-2"><i class="fas fa-tag"></i> ${tarefa.etiqueta}</div>` : '';
        const usuario = tarefa.nome_usuario_atribuido ? 
            `<div class="small text-muted mt-2"><i class="fas fa-user"></i> <strong>Atribuído:</strong> ${tarefa.nome_usuario_atribuido}</div>` : '';

        return `
            <div class="fw-bold text-primary">${this.escapeHtml(tarefa.titulo)}</div>
            <div class="text-muted small mt-1">${this.escapeHtml(tarefa.descricao || '')}</div>
            ${dataEvento}
            ${prioridade}
            ${etiqueta}
            ${usuario}
            <div class="kanban-actions mt-2">
                <button class="btn-sm btn-outline-info btn-ver-tratativas" title="Ver Tratativas" data-id="${tarefa.id}">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="btn-sm btn-outline-secondary btn-editar" title="Editar Tarefa" 
                        data-task='${JSON.stringify(tarefa)}'>
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn-sm btn-outline-danger btn-excluir" title="Excluir Tarefa" data-id="${tarefa.id}">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
    }

    atualizarContadores() {
        ['afazer', 'progresso', 'concluido'].forEach(status => {
            const count = this.todasAsTarefas[status].length;
            const badge = document.getElementById(`count-${status}`);
            if (badge) {
                badge.textContent = count;
            }
        });
    }

    filtrarTarefas() {
        const termoBusca = document.getElementById('filtroBusca').value.toLowerCase();
        const prioridadeFiltro = document.getElementById('filtroPrioridade').value;
        
        const tarefasFiltradas = {
            afazer: [],
            progresso: [],
            concluido: []
        };

        ['afazer', 'progresso', 'concluido'].forEach(status => {
            this.todasAsTarefas[status].forEach(tarefa => {
                let incluir = true;

                if (termoBusca && !this.tarefaContemTermo(tarefa, termoBusca)) {
                    incluir = false;
                }

                if (prioridadeFiltro && tarefa.prioridade !== prioridadeFiltro) {
                    incluir = false;
                }

                if (incluir) {
                    tarefasFiltradas[status].push(tarefa);
                }
            });
        });

        this.renderizarTarefas(tarefasFiltradas);
    }

    tarefaContemTermo(tarefa, termo) {
        return tarefa.titulo.toLowerCase().includes(termo) ||
               (tarefa.descricao && tarefa.descricao.toLowerCase().includes(termo)) ||
               (tarefa.nome_usuario_atribuido && tarefa.nome_usuario_atribuido.toLowerCase().includes(termo)) ||
               (tarefa.etiqueta && tarefa.etiqueta.toLowerCase().includes(termo));
    }

    limparFiltro() {
        document.getElementById('filtroBusca').value = '';
        document.getElementById('filtroPrioridade').value = '';
        this.renderizarTarefas(this.todasAsTarefas);
    }

    async adicionarTarefa(e) {
        e.preventDefault();
        
        const dados = {
            titulo: document.getElementById('adicionar_titulo').value.trim(),
            descricao: document.getElementById('adicionar_descricao').value.trim(),
            data_evento: document.getElementById('adicionar_data').value,
            prioridade: document.getElementById('adicionar_prioridade').value,
            etiqueta: document.getElementById('adicionar_etiqueta').value.trim(),
            usuario_atribuido: document.getElementById('adicionar_usuario_atribuido').value
        };

        if (!dados.titulo) {
            this.showToast('O título da tarefa é obrigatório.', 'warning');
            return;
        }

        try {
            const resposta = await fetch('adicionar_tarefa.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(dados)
            });

            const resultado = await resposta.json();
            if (resultado.sucesso) {
                bootstrap.Modal.getInstance(document.getElementById('modalAdicionarTarefa')).hide();
                this.showToast('Tarefa adicionada com sucesso!', 'success');
                document.getElementById('formAdicionarTarefa').reset();
                await this.carregarTarefas();
            } else {
                this.showToast('Erro ao adicionar tarefa: ' + resultado.erro, 'danger');
            }
        } catch (error) {
            console.error('Erro na requisição:', error);
            this.showToast('Erro de conexão. Tente novamente.', 'danger');
        }
    }

    async editarTarefa(e) {
        e.preventDefault();
        
        const dados = {
            id: document.getElementById('editar_id').value,
            titulo: document.getElementById('editar_titulo').value.trim(),
            descricao: document.getElementById('editar_descricao').value.trim(),
            data_evento: document.getElementById('editar_data').value,
            prioridade: document.getElementById('editar_prioridade').value,
            etiqueta: document.getElementById('editar_etiqueta').value.trim(),
            usuario_atribuido: document.getElementById('editar_usuario_atribuido').value
        };

        if (!dados.titulo) {
            this.showToast('O título da tarefa é obrigatório.', 'warning');
            return;
        }

        try {
            const resposta = await fetch('editar_tarefa.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(dados)
            });

            const resultado = await resposta.json();
            if (resultado.sucesso) {
                bootstrap.Modal.getInstance(document.getElementById('modalEditarTarefa')).hide();
                this.showToast('Tarefa editada com sucesso!', 'success');
                await this.carregarTarefas();
            } else {
                this.showToast('Erro ao editar tarefa: ' + resultado.erro, 'danger');
            }
        } catch (error) {
            console.error('Erro na requisição:', error);
            this.showToast('Erro de conexão. Tente novamente.', 'danger');
        }
    }

    abrirModalTratativa(tarefaId, statusAnterior, novoStatus) {
        const statusTexto = {
            'afazer': 'A Fazer',
            'progresso': 'Em Progresso', 
            'concluido': 'Concluído'
        };

        document.getElementById('tratativa_tarefa_id').value = tarefaId;
        document.getElementById('tratativa_status_anterior').value = statusAnterior;
        document.getElementById('tratativa_status_novo').value = novoStatus;
        document.getElementById('texto_mudanca_status').textContent = 
            `${statusTexto[statusAnterior]} → ${statusTexto[novoStatus]}`;
        document.getElementById('tratativa_texto').value = '';

        const modal = new bootstrap.Modal(document.getElementById('modalTratativa'));
        modal.show();
    }

    async salvarTratativa(e) {
        e.preventDefault();

        const dados = {
            tarefa_id: document.getElementById('tratativa_tarefa_id').value,
            novo_status: document.getElementById('tratativa_status_novo').value,
            tratativa: document.getElementById('tratativa_texto').value.trim()
        };

        if (!dados.tratativa) {
            this.showToast('A descrição da tratativa é obrigatória.', 'warning');
            return;
        }

        try {
            const resposta = await fetch('atualizar_tarefa.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(dados)
            });

            const resultado = await resposta.json();
            if (resultado.sucesso) {
                bootstrap.Modal.getInstance(document.getElementById('modalTratativa')).hide();
                this.showToast('Tarefa movida com sucesso!', 'success');
                this.tarefaSendoMovida = null;
                await this.carregarTarefas();
            } else {
                this.showToast('Erro ao mover tarefa: ' + resultado.erro, 'danger');
                this.cancelarMudancaStatus();
            }
        } catch (error) {
            console.error('Erro na requisição:', error);
            this.showToast('Erro de conexão. Tente novamente.', 'danger');
            this.cancelarMudancaStatus();
        }
    }

    cancelarMudancaStatus() {
        if (this.tarefaSendoMovida) {
            // Voltar elemento para posição original
            this.tarefaSendoMovida.containerOrigem.appendChild(this.tarefaSendoMovida.elemento);
            this.tarefaSendoMovida = null;
        }
        
        // Fechar modal se estiver aberto
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalTratativa'));
        if (modal) {
            modal.hide();
        }
    }

    async abrirModalTratativasHistorico(tarefaId) {
        try {
            const resposta = await fetch(`buscar_tratativas.php?tarefa_id=${tarefaId}`);
            const dados = await resposta.json();

            if (!dados.sucesso) {
                this.showToast('Erro ao carregar tratativas: ' + dados.erro, 'danger');
                return;
            }

            document.getElementById('historico_titulo_tarefa').textContent = dados.tarefa.titulo;
            
            const listaTratativas = document.getElementById('lista_tratativas');
            if (dados.tratativas.length === 0) {
                listaTratativas.innerHTML = '<p class="text-muted">Nenhuma tratativa registrada ainda.</p>';
            } else {
                listaTratativas.innerHTML = dados.tratativas.map(tratativa => {
                    const statusTexto = {
                        'afazer': 'A Fazer',
                        'progresso': 'Em Progresso', 
                        'concluido': 'Concluído'
                    };

                    return `
                        <div class="tratativa-item">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <span class="status-badge status-${tratativa.status_anterior}">${statusTexto[tratativa.status_anterior]}</span>
                                    <i class="fas fa-arrow-right mx-2"></i>
                                    <span class="status-badge status-${tratativa.status_novo}">${statusTexto[tratativa.status_novo]}</span>
                                </div>
                                <small class="text-muted">${this.formatarDataHora(tratativa.data_tratativa)}</small>
                            </div>
                            <p class="mb-1">${this.escapeHtml(tratativa.tratativa)}</p>
                            <small class="text-muted">por <strong>${tratativa.nome_usuario}</strong></small>
                        </div>
                    `;
                }).join('');
            }

            const modal = new bootstrap.Modal(document.getElementById('modalHistoricoTratativas'));
            modal.show();

        } catch (error) {
            console.error('Erro ao buscar tratativas:', error);
            this.showToast('Erro de conexão ao buscar tratativas.', 'danger');
        }
    }

    abrirModalEditar(tarefa) {
        document.getElementById('editar_id').value = tarefa.id;
        document.getElementById('editar_titulo').value = tarefa.titulo;
        document.getElementById('editar_descricao').value = tarefa.descricao || '';
        document.getElementById('editar_data').value = tarefa.data_evento || '';
        document.getElementById('editar_prioridade').value = tarefa.prioridade || '';
        document.getElementById('editar_etiqueta').value = tarefa.etiqueta || '';
        document.getElementById('editar_usuario_atribuido').value = tarefa.usuario_atribuido_id || '';

        const modal = new bootstrap.Modal(document.getElementById('modalEditarTarefa'));
        modal.show();
    }

    abrirModalExcluir(tarefaId) {
        document.getElementById('excluir_tarefa_id').value = tarefaId;
        const modal = new bootstrap.Modal(document.getElementById('modalConfirmarExclusao'));
        modal.show();
    }

    async confirmarExclusao() {
        const tarefaId = document.getElementById('excluir_tarefa_id').value;

        try {
            const resposta = await fetch('excluir_tarefa.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: tarefaId })
            });

            const resultado = await resposta.json();
            if (resultado.sucesso) {
                bootstrap.Modal.getInstance(document.getElementById('modalConfirmarExclusao')).hide();
                this.showToast('Tarefa excluída com sucesso!', 'success');
                await this.carregarTarefas();
            } else {
                this.showToast('Erro ao excluir tarefa: ' + resultado.erro, 'danger');
            }
        } catch (error) {
            console.error('Erro na requisição:', error);
            this.showToast('Erro de conexão. Tente novamente.', 'danger');
        }
    }

    // Métodos utilitários
    formatarData(dataString) {
        if (!dataString) return '';
        const [ano, mes, dia] = dataString.split('-');
        return `${dia}/${mes}/${ano}`;
    }

    formatarDataHora(dateTimeString) {
        if (!dateTimeString) return '';
        const date = new Date(dateTimeString);
        return date.toLocaleString('pt-BR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    showToast(message, type = 'info') {
        const toastContainer = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type} border-0 fade show`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;

        toastContainer.appendChild(toast);
        const bsToast = new bootstrap.Toast(toast, { delay: 4000 });
        bsToast.show();

        toast.addEventListener('hidden.bs.toast', () => toast.remove());
    }
}

// Instância global
let kanbanManager;

// Funções globais para compatibilidade
document.addEventListener('DOMContentLoaded', () => {
    kanbanManager = new KanbanManager();
});

function filtrarTarefas() {
    if (kanbanManager) {
        kanbanManager.filtrarTarefas();
    }
}

function limparFiltro() {
    if (kanbanManager) {
        kanbanManager.limparFiltro();
    }
}

function carregarTarefas() {
    if (kanbanManager) {
        kanbanManager.carregarTarefas();
    }
}

function cancelarMudancaStatus() {
    if (kanbanManager) {
        kanbanManager.cancelarMudancaStatus();
    }
}

function confirmarExclusao() {
    if (kanbanManager) {
        kanbanManager.confirmarExclusao();
    }
}
</script>

</body>
</html>