<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}
require 'conexao.php'; // Certifique-se de que conexao.php está configurado corretamente
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
        /* CSS incorporado para evitar dependência de arquivo externo */
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
    </style>
</head>
<body>

<?php 
// Incluir navegação se existir, senão criar uma básica
if (file_exists('nav.php')) {
    include 'nav.php';
} else {
    echo '<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container">
                <a class="navbar-brand" href="#">Sistema Meraki</a>
                <div class="navbar-nav ms-auto">
                    <a class="nav-link" href="logout.php">Sair</a>
                </div>
            </div>
          </nav>';
}
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Painel de Tarefas (Kanban)</h2>
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
                <input type="text" id="filtroBusca" class="form-control" placeholder="Buscar tarefa por título, descrição ou usuário..." onkeyup="filtrarTarefas()">
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

<!-- Modais -->
<?php 
if (file_exists('modais_kanban.php')) {
    include 'modais_kanban.php';
} else {
    // Modais básicos inline se o arquivo não existir
    echo '
    <!-- Modal Adicionar Tarefa -->
    <div class="modal fade" id="modalAdicionarTarefa" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Adicionar Nova Tarefa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="formAdicionarTarefa">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Título *</label>
                            <input type="text" class="form-control" id="adicionar_titulo" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descrição</label>
                            <textarea class="form-control" id="adicionar_descricao" rows="3"></textarea>
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
                            <input type="text" class="form-control" id="adicionar_etiqueta">
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
                        <button type="submit" class="btn btn-primary">Adicionar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>';
}
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dragula/3.7.2/dragula.min.js"></script>

<script>
// Classe para gerenciar o Kanban
class KanbanManager {
    constructor() {
        this.todasAsTarefas = {
            afazer: [],
            progresso: [],
            concluido: []
        };
        this.todosOsUsuarios = [];
        this.dragula = null;
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
                this.abrirModalTratativa(tarefaId, statusAnterior, novoStatus);
            }
        });
    }

    configurarEventListeners() {
        // Formulário de adicionar tarefa
        const formAdicionar = document.getElementById('formAdicionarTarefa');
        if (formAdicionar) {
            formAdicionar.addEventListener('submit', (e) => this.adicionarTarefa(e));
        }

        // Event delegation para botões das tarefas
        document.querySelectorAll('.kanban-tarefas').forEach(container => {
            container.addEventListener('click', (event) => {
                const target = event.target;
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
        const prioridadeClass = tarefa.prioridade ? `prioridade-${tarefa.prioridade.toLowerCase()}` : '';
        const dataEvento = tarefa.data_evento ? `<div class="small"><i class="fas fa-calendar"></i> ${this.formatarData(tarefa.data_evento)}</div>` : '';
        const prioridade = tarefa.prioridade ? `<div class="small ${prioridadeClass}"><i class="fas fa-exclamation-circle"></i> ${tarefa.prioridade}</div>` : '';
        const etiqueta = tarefa.etiqueta ? `<div class="etiqueta"><i class="fas fa-tag"></i> ${tarefa.etiqueta}</div>` : '';
        const usuario = tarefa.nome_usuario_atribuido ? `<div class="small mt-2"><i class="fas fa-user"></i> <strong>Atribuído a:</strong> ${tarefa.nome_usuario_atribuido}</div>` : '';

        return `
            <div class="fw-bold">${this.escapeHtml(tarefa.titulo)}</div>
            <div class="text-muted small">${this.escapeHtml(tarefa.descricao)}</div>
            ${dataEvento}
            ${prioridade}
            ${etiqueta}
            ${usuario}
            <div class="kanban-actions">
                <button class="btn-sm btn-info btn-ver-tratativas" title="Ver Tratativas" data-id="${tarefa.id}">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="btn-sm btn-secondary btn-editar" title="Editar Tarefa" data-task='${JSON.stringify(tarefa)}'>
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn-sm btn-danger btn-excluir" title="Excluir Tarefa" data-id="${tarefa.id}">
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

                // Filtro de busca
                if (termoBusca && !this.tarefaContemTermo(tarefa, termoBusca)) {
                    incluir = false;
                }

                // Filtro de prioridade
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
               tarefa.descricao.toLowerCase().includes(termo) ||
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

    // Métodos de modal (implementar conforme necessário)
    abrirModalTratativa(tarefaId, statusAnterior, novoStatus) {
        // Implementar modal de tratativa
        console.log('Abrir modal tratativa:', tarefaId, statusAnterior, novoStatus);
    }

    abrirModalTratativasHistorico(tarefaId) {
        // Implementar modal de histórico
        console.log('Abrir histórico:', tarefaId);
    }

    abrirModalEditar(tarefa) {
        // Implementar modal de edição
        console.log('Editar tarefa:', tarefa);
    }

    abrirModalExcluir(tarefaId) {
        // Implementar modal de exclusão
        console.log('Excluir tarefa:', tarefaId);
    }
}

// Funções globais para compatibilidade
let kanbanManager;

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
</script>

</body>
</html>