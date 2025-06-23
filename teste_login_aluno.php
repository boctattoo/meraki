<?php
session_start();
// Apenas administradores logados podem usar esta ferramenta
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}
require 'conexao.php';

$resultado = null;
$identifier_buscado = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['identifier'])) {
    $identifier_buscado = trim($_POST['identifier']);
    $telefone_numerico = preg_replace('/[^0-9]/', '', $identifier_buscado);

    try {
        $stmt = $pdo->prepare(
            "SELECT 
                a.id, a.nome, a.email, a.telefone, a.cpf as cpf_aluno, a.senha, a.data_nascimento, 
                c.cpf_cnpj_aluno as cpf_contrato
             FROM alunos a 
             LEFT JOIN contratos c ON a.id = c.aluno_id
             WHERE a.cpf = :identifier 
                OR a.telefone = :telefone 
                OR a.email = :identifier
                OR c.cpf_cnpj_aluno = :identifier
             LIMIT 1"
        );
        $stmt->execute([
            ':identifier' => $identifier_buscado, 
            ':telefone' => $telefone_numerico
        ]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        $erro = "Erro na consulta: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste de Login de Aluno</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'nav.php'; ?>

    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="fas fa-vial me-2"></i>Ferramenta de Diagnóstico de Login de Aluno</h4>
            </div>
            <div class="card-body">
                <p class="card-text">Use esta página para verificar os dados de um aluno a partir do seu identificador (CPF, telemóvel ou e-mail) e confirmar se a senha inicial está correta.</p>
                <form method="POST" class="row g-3 align-items-end">
                    <div class="col-md-8">
                        <label for="identifier" class="form-label">CPF, Celular ou Email do Aluno</label>
                        <input type="text" class="form-control" name="identifier" id="identifier" value="<?php echo htmlspecialchars($identifier_buscado); ?>" required>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-2"></i>Verificar Dados
                        </button>
                    </div>
                </form>

                <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                    <hr class="my-4">
                    <h5 class="mb-3">Resultado da Verificação</h5>
                    <?php if (isset($erro)): ?>
                        <div class="alert alert-danger"><?php echo $erro; ?></div>
                    <?php elseif ($resultado): ?>
                        <div class="alert alert-info">
                            <strong>Aluno Encontrado:</strong> <?php echo htmlspecialchars($resultado['nome']); ?> (ID: <?php echo $resultado['id']; ?>)
                        </div>
                        <ul class="list-group">
                            <li class="list-group-item"><strong>Campo `senha` no banco:</strong> 
                                <?php if (empty($resultado['senha'])): ?>
                                    <span class="badge bg-warning text-dark">VAZIO / NULL</span> (Indica primeiro acesso)
                                <?php else: ?>
                                    <span class="badge bg-success">PREENCHIDO</span> (Aluno já definiu uma senha)
                                <?php endif; ?>
                            </li>
                            <li class="list-group-item"><strong>Senha Padrão (Data de Nascimento):</strong> 
                                <?php 
                                    $data_nasc_formatada = $resultado['data_nascimento'] ? date('dmY', strtotime($resultado['data_nascimento'])) : 'N/A';
                                    echo "<code>" . $data_nasc_formatada . "</code>";
                                ?>
                            </li>
                             <li class="list-group-item"><strong>Senha Padrão (CPF):</strong> 
                                <?php 
                                    $cpf_numerico = preg_replace('/[^0-9]/', '', $resultado['cpf_aluno'] ?? $resultado['cpf_contrato'] ?? '');
                                    echo "<code>" . ($cpf_numerico ?: 'N/A') . "</code>";
                                ?>
                            </li>
                        </ul>
                        <div class="mt-3">
                            <h6>Análise:</h6>
                            <p>
                                <?php if (empty($resultado['senha'])): ?>
                                    O campo da senha está vazio. Para o primeiro acesso, o aluno deve usar uma das duas senhas padrão acima. Se mesmo assim der erro, verifique se a data de nascimento e o CPF estão corretos no cadastro do aluno.
                                <?php else: ?>
                                    Este aluno já possui uma senha definida. Ele deve usar a senha que criou, e não a senha padrão.
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">Nenhum aluno foi encontrado com o identificador "<?php echo htmlspecialchars($identifier_buscado); ?>".</div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```

### **Como Usar a Ferramenta de Teste:**

1.  Acesse `teste_login_aluno.php` no seu navegador (é preciso estar logado como administrador).
2.  Insira o CPF, telemóvel ou e-mail do aluno que está com problemas.
3.  Clique em "Verificar Dados".

O resultado irá mostrar-lhe:
* Se o campo `senha` para aquele aluno está **VAZIO** ou **PREENCHIDO**.
* Quais são as duas senhas padrão que o sistema espera para o primeiro acesso.

Com esta informação, você poderá identificar a causa exata do proble