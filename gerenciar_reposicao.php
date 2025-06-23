<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}
require 'conexao.php';

// --- Ações do POST ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['bloquear_data'])) {
        $data_bloqueio = $_POST['data_bloqueio'];
        // Se "dia_todo" for selecionado, o horário é NULL.
        $horario_bloqueio = $_POST['horario_bloqueio'] === 'dia_todo' ? null : $_POST['horario_bloqueio'];
        
        $stmt = $pdo->prepare("INSERT INTO reposicoes_horarios_bloqueados (data_bloqueada, horario_bloqueado, usuario_id) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE data_bloqueada=data_bloqueada");
        $stmt->execute([$data_bloqueio, $horario_bloqueio, $_SESSION['usuario_id']]);
    }
    if (isset($_POST['desbloquear_data'])) {
        $id_desbloqueio = $_POST['id_desbloqueio'];
        $stmt = $pdo->prepare("DELETE FROM reposicoes_horarios_bloqueados WHERE id = ?");
        $stmt->execute([$id_desbloqueio]);
    }
    header('Location: gerenciar_reposicao.php');
    exit;
}

// --- Busca de Dados ---
$agendamentos = $pdo->query("SELECT * FROM reposicoes_agendadas WHERE data_reposicao >= CURDATE() AND status = 'agendada' ORDER BY data_reposicao, horario_reposicao")->fetchAll(PDO::FETCH_ASSOC);
$datas_bloqueadas = $pdo->query("SELECT * FROM reposicoes_horarios_bloqueados WHERE data_bloqueada >= CURDATE() ORDER BY data_bloqueada, horario_bloqueado")->fetchAll(PDO::FETCH_ASSOC);
$horarios_base = ["08:00 - 10:00", "10:00 - 12:00", "13:00 - 14:00", "15:00 - 17:00", "17:00 - 19:00", "19:00 - 21:00"];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerir Reposições</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<?php include 'nav.php'; ?>
<div class="container mt-4">
    <h2><i class="fas fa-cogs me-2"></i>Gerir Aulas de Reposição</h2>

    <div class="row mt-4">
        <!-- Agendamentos Futuros -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header fw-bold">Próximos Agendamentos</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead><tr><th>Aluno</th><th>Data</th><th>Horário</th><th>Telefone</th></tr></thead>
                            <tbody>
                                <?php foreach($agendamentos as $ag): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($ag['nome_aluno']); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($ag['data_reposicao'])); ?></td>
                                        <td><?php echo htmlspecialchars($ag['horario_reposicao']); ?></td>
                                        <td><?php echo htmlspecialchars($ag['telefone_aluno']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if(empty($agendamentos)) echo '<tr><td colspan="4" class="text-center">Nenhum agendamento futuro.</td></tr>'; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bloqueio de Datas e Horários -->
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header fw-bold">Bloquear Horário ou Dia</div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3"><label class="form-label">Data a ser bloqueada</label><input type="date" name="data_bloqueio" class="form-control" required></div>
                        <div class="mb-3">
                            <label class="form-label">Horário Específico (opcional)</label>
                            <select name="horario_bloqueio" class="form-select">
                                <option value="dia_todo">O dia todo</option>
                                <?php foreach($horarios_base as $h): ?>
                                    <option value="<?php echo $h; ?>"><?php echo $h; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" name="bloquear_data" class="btn btn-danger w-100">Bloquear</button>
                    </form>
                </div>
            </div>
             <div class="card">
                <div class="card-header fw-bold">Horários Bloqueados</div>
                 <ul class="list-group list-group-flush">
                    <?php foreach($datas_bloqueadas as $db): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                           <div>
                               <strong><?php echo date('d/m/Y', strtotime($db['data_bloqueada'])); ?></strong><br>
                               <small class="text-muted"><?php echo htmlspecialchars($db['horario_bloqueado'] ?? 'Dia todo'); ?></small>
                           </div>
                           <form method="POST" class="d-inline" onsubmit="return confirm('Deseja desbloquear este horário?');">
                               <input type="hidden" name="id_desbloqueio" value="<?php echo $db['id']; ?>"><button type="submit" name="desbloquear_data" class="btn btn-sm btn-outline-success" title="Desbloquear"><i class="fas fa-unlock"></i></button>
                           </form>
                        </li>
                    <?php endforeach; ?>
                    <?php if(empty($datas_bloqueadas)) echo '<li class="list-group-item text-center">Nenhuma data ou horário bloqueado.</li>'; ?>
                 </ul>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
