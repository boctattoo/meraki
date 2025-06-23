<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['usuario_id'])) return;
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="index.php"><i class="fas fa-graduation-cap me-2"></i>Sistema Meraki</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbarCollapse"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="mainNavbarCollapse">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <!-- Menu Alunos -->
                <li class="nav-item dropdown"><a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"><i class="fas fa-users me-1"></i> Alunos</a><ul class="dropdown-menu"><li><a class="dropdown-item" href="cadastro_aluno.php"><i class="fas fa-user-plus fa-fw me-2"></i>Cadastrar Aluno</a></li><li><a class="dropdown-item" href="buscar_aluno.php"><i class="fas fa-search fa-fw me-2"></i>Buscar/Editar Aluno</a></li></ul></li>
                <!-- Menu Académico -->
                <li class="nav-item dropdown"><a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"><i class="fas fa-book-open me-1"></i> Académico</a><ul class="dropdown-menu"><li><a class="dropdown-item" href="mapa_turmas.php"><i class="fas fa-map-marked-alt fa-fw me-2"></i>Mapa de Turmas</a></li><li><a class="dropdown-item" href="lancar_presenca.php"><i class="fas fa-user-check fa-fw me-2"></i>Lançar Presença</a></li><li><a class="dropdown-item" href="lista_presenca.php"><i class="fas fa-list-alt fa-fw me-2"></i>Relatório de Presença</a></li><li><a class="dropdown-item" href="dashboard_presenca.php"><i class="fas fa-chart-line fa-fw me-2"></i>Dashboard de Presença</a></li><li><hr class="dropdown-divider"></li><li><a class="dropdown-item" href="notificar_faltas.php"><i class="fab fa-whatsapp fa-fw me-2"></i>Notificar Faltas</a></li><li><a class="dropdown-item" href="gerenciar_reposicao.php"><i class="fas fa-cogs fa-fw me-2"></i>Gerir Reposição</a></li></ul></li>
                <!-- Menu Contratos -->
                <li class="nav-item dropdown"><a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"><i class="fas fa-file-invoice-dollar me-1"></i> Contratos</a><ul class="dropdown-menu"><li><a class="dropdown-item" href="precadastro.php"><i class="fas fa-file-signature fa-fw me-2"></i>Novo Contrato</a></li><li><a class="dropdown-item" href="visualizar_contratos.php"><i class="fas fa-folder-open fa-fw me-2"></i>Visualizar Contratos</a></li></ul></li>
                <!-- Link Kanban -->
                <li class="nav-item"><a class="nav-link" href="kanban.php"><i class="fas fa-tasks me-1"></i> Kanban de Tarefas</a></li>
            </ul>
            <ul class="navbar-nav ms-auto"><li class="nav-item dropdown"><a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown"><i class="fas fa-user-circle me-1"></i> <?php echo htmlspecialchars($_SESSION['usuario_nome'] ?? 'Usuário'); ?></a><ul class="dropdown-menu dropdown-menu-end"><li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt fa-fw me-2"></i>Sair</a></li></ul></li></ul>
        </div>
    </div>
</nav>
