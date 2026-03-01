<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';

$pdo = db();
$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    if ($acao === 'cadastrar_academia') {
        $stmt = $pdo->prepare('INSERT INTO academias (nome, cidade, estilo_principal, telefone, criado_em) VALUES (:nome, :cidade, :estilo, :telefone, :criado_em)');
        $stmt->execute([
            ':nome' => trim($_POST['nome_academia'] ?? ''),
            ':cidade' => trim($_POST['cidade'] ?? ''),
            ':estilo' => trim($_POST['estilo_principal'] ?? ''),
            ':telefone' => trim($_POST['telefone'] ?? ''),
            ':criado_em' => date('Y-m-d H:i:s'),
        ]);
        $mensagem = 'Academia cadastrada com sucesso.';
    }

    if ($acao === 'cadastrar_instrutor') {
        $stmt = $pdo->prepare('INSERT INTO instrutores (academia_id, nome, graduacao, email) VALUES (:academia_id, :nome, :graduacao, :email)');
        $stmt->execute([
            ':academia_id' => (int) ($_POST['academia_instrutor'] ?? 0),
            ':nome' => trim($_POST['nome_instrutor'] ?? ''),
            ':graduacao' => trim($_POST['graduacao'] ?? ''),
            ':email' => trim($_POST['email_instrutor'] ?? ''),
        ]);
        $mensagem = 'Instrutor cadastrado com sucesso.';
    }

    if ($acao === 'cadastrar_aluno') {
        $stmt = $pdo->prepare('INSERT INTO alunos (academia_id, nome, faixa, idade, status_matricula) VALUES (:academia_id, :nome, :faixa, :idade, :status)');
        $stmt->execute([
            ':academia_id' => (int) ($_POST['academia_aluno'] ?? 0),
            ':nome' => trim($_POST['nome_aluno'] ?? ''),
            ':faixa' => trim($_POST['faixa'] ?? ''),
            ':idade' => (int) ($_POST['idade'] ?? 0),
            ':status' => trim($_POST['status_matricula'] ?? 'Ativo'),
        ]);
        $mensagem = 'Aluno cadastrado com sucesso.';
    }

    if ($acao === 'lancar_mensalidade') {
        $stmt = $pdo->prepare('INSERT INTO mensalidades (aluno_id, referencia, valor, pago) VALUES (:aluno_id, :referencia, :valor, :pago)');
        $stmt->execute([
            ':aluno_id' => (int) ($_POST['aluno_id'] ?? 0),
            ':referencia' => trim($_POST['referencia'] ?? ''),
            ':valor' => (float) ($_POST['valor'] ?? 0),
            ':pago' => isset($_POST['pago']) ? 1 : 0,
        ]);
        $mensagem = 'Mensalidade lançada com sucesso.';
    }

    if ($acao === 'marcar_pago') {
        $stmt = $pdo->prepare('UPDATE mensalidades SET pago = 1 WHERE id = :id');
        $stmt->execute([':id' => (int) ($_POST['mensalidade_id'] ?? 0)]);
        $mensagem = 'Mensalidade marcada como paga.';
    }
}

$academias = $pdo->query('SELECT * FROM academias ORDER BY nome')->fetchAll(PDO::FETCH_ASSOC);
$instrutores = $pdo->query('SELECT i.*, a.nome AS academia_nome FROM instrutores i JOIN academias a ON a.id = i.academia_id ORDER BY i.nome')->fetchAll(PDO::FETCH_ASSOC);
$alunos = $pdo->query('SELECT al.*, a.nome AS academia_nome FROM alunos al JOIN academias a ON a.id = al.academia_id ORDER BY al.nome')->fetchAll(PDO::FETCH_ASSOC);
$mensalidades = $pdo->query('SELECT m.*, al.nome AS aluno_nome FROM mensalidades m JOIN alunos al ON al.id = m.aluno_id ORDER BY m.referencia DESC')->fetchAll(PDO::FETCH_ASSOC);

$totalAlunos = (int) $pdo->query('SELECT COUNT(*) FROM alunos')->fetchColumn();
$totalInstrutores = (int) $pdo->query('SELECT COUNT(*) FROM instrutores')->fetchColumn();
$totalAcademias = (int) $pdo->query('SELECT COUNT(*) FROM academias')->fetchColumn();
$totalEmAberto = (float) $pdo->query('SELECT COALESCE(SUM(valor), 0) FROM mensalidades WHERE pago = 0')->fetchColumn();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DojoPro Manager - Sistema Legado</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="wrapper">
        <header>
            <h1>DojoPro Manager</h1>
            <p>Sistema legado de gestão para academias de artes marciais</p>
        </header>

        <?php if ($mensagem !== ''): ?>
            <div class="alerta"><?php echo htmlspecialchars($mensagem); ?></div>
        <?php endif; ?>

        <section class="painel-resumo">
            <div><strong>Academias:</strong> <?php echo $totalAcademias; ?></div>
            <div><strong>Instrutores:</strong> <?php echo $totalInstrutores; ?></div>
            <div><strong>Alunos:</strong> <?php echo $totalAlunos; ?></div>
            <div><strong>Mensalidades em aberto:</strong> R$ <?php echo number_format($totalEmAberto, 2, ',', '.'); ?></div>
        </section>

        <section class="bloco">
            <h2>Cadastro de Academia</h2>
            <form method="post" class="form-legado">
                <input type="hidden" name="acao" value="cadastrar_academia">
                <label>Nome da academia <input name="nome_academia" required></label>
                <label>Cidade <input name="cidade" required></label>
                <label>Estilo principal <input name="estilo_principal" placeholder="Karatê, Jiu-Jitsu..." required></label>
                <label>Telefone <input name="telefone"></label>
                <button type="submit">Cadastrar</button>
            </form>
        </section>

        <section class="bloco">
            <h2>Cadastro de Instrutor</h2>
            <form method="post" class="form-legado">
                <input type="hidden" name="acao" value="cadastrar_instrutor">
                <label>Academia
                    <select name="academia_instrutor" required>
                        <option value="">Selecione</option>
                        <?php foreach ($academias as $academia): ?>
                            <option value="<?php echo (int) $academia['id']; ?>"><?php echo htmlspecialchars($academia['nome']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>Nome <input name="nome_instrutor" required></label>
                <label>Graduação <input name="graduacao" placeholder="Faixa preta 3º dan" required></label>
                <label>E-mail <input name="email_instrutor" type="email"></label>
                <button type="submit">Cadastrar</button>
            </form>
        </section>

        <section class="bloco">
            <h2>Cadastro de Aluno</h2>
            <form method="post" class="form-legado">
                <input type="hidden" name="acao" value="cadastrar_aluno">
                <label>Academia
                    <select name="academia_aluno" required>
                        <option value="">Selecione</option>
                        <?php foreach ($academias as $academia): ?>
                            <option value="<?php echo (int) $academia['id']; ?>"><?php echo htmlspecialchars($academia['nome']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>Nome <input name="nome_aluno" required></label>
                <label>Faixa <input name="faixa" placeholder="Branca, Azul, Roxa..." required></label>
                <label>Idade <input name="idade" type="number" min="3" max="99" required></label>
                <label>Status
                    <select name="status_matricula">
                        <option>Ativo</option>
                        <option>Em pausa</option>
                        <option>Inadimplente</option>
                        <option>Cancelado</option>
                    </select>
                </label>
                <button type="submit">Cadastrar</button>
            </form>
        </section>

        <section class="bloco">
            <h2>Lançar Mensalidade</h2>
            <form method="post" class="form-legado">
                <input type="hidden" name="acao" value="lancar_mensalidade">
                <label>Aluno
                    <select name="aluno_id" required>
                        <option value="">Selecione</option>
                        <?php foreach ($alunos as $aluno): ?>
                            <option value="<?php echo (int) $aluno['id']; ?>"><?php echo htmlspecialchars($aluno['nome']); ?> (<?php echo htmlspecialchars($aluno['academia_nome']); ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>Referência <input name="referencia" placeholder="2026-03" required></label>
                <label>Valor (R$) <input name="valor" type="number" step="0.01" min="0" required></label>
                <label class="check"><input type="checkbox" name="pago" value="1"> Pago no ato</label>
                <button type="submit">Lançar</button>
            </form>
        </section>

        <section class="bloco">
            <h2>Academias Cadastradas</h2>
            <table>
                <thead>
                    <tr><th>Nome</th><th>Cidade</th><th>Estilo</th><th>Telefone</th><th>Cadastro</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($academias as $academia): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($academia['nome']); ?></td>
                            <td><?php echo htmlspecialchars($academia['cidade']); ?></td>
                            <td><?php echo htmlspecialchars($academia['estilo_principal']); ?></td>
                            <td><?php echo htmlspecialchars($academia['telefone'] ?: '-'); ?></td>
                            <td><?php echo htmlspecialchars($academia['criado_em']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <section class="bloco">
            <h2>Equipe de Instrutores</h2>
            <table>
                <thead>
                    <tr><th>Nome</th><th>Academia</th><th>Graduação</th><th>E-mail</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($instrutores as $instrutor): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($instrutor['nome']); ?></td>
                            <td><?php echo htmlspecialchars($instrutor['academia_nome']); ?></td>
                            <td><?php echo htmlspecialchars($instrutor['graduacao']); ?></td>
                            <td><?php echo htmlspecialchars($instrutor['email'] ?: '-'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <section class="bloco">
            <h2>Alunos</h2>
            <table>
                <thead>
                    <tr><th>Nome</th><th>Academia</th><th>Faixa</th><th>Idade</th><th>Status</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($alunos as $aluno): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($aluno['nome']); ?></td>
                            <td><?php echo htmlspecialchars($aluno['academia_nome']); ?></td>
                            <td><?php echo htmlspecialchars($aluno['faixa']); ?></td>
                            <td><?php echo (int) $aluno['idade']; ?></td>
                            <td><?php echo htmlspecialchars($aluno['status_matricula']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <section class="bloco">
            <h2>Controle de Mensalidades</h2>
            <table>
                <thead>
                    <tr><th>Aluno</th><th>Referência</th><th>Valor</th><th>Status</th><th>Ação</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($mensalidades as $mensalidade): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($mensalidade['aluno_nome']); ?></td>
                            <td><?php echo htmlspecialchars($mensalidade['referencia']); ?></td>
                            <td>R$ <?php echo number_format((float) $mensalidade['valor'], 2, ',', '.'); ?></td>
                            <td><?php echo (int) $mensalidade['pago'] === 1 ? 'Pago' : 'Em aberto'; ?></td>
                            <td>
                                <?php if ((int) $mensalidade['pago'] === 0): ?>
                                    <form method="post">
                                        <input type="hidden" name="acao" value="marcar_pago">
                                        <input type="hidden" name="mensalidade_id" value="<?php echo (int) $mensalidade['id']; ?>">
                                        <button type="submit">Marcar como pago</button>
                                    </form>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </div>
</body>
</html>
