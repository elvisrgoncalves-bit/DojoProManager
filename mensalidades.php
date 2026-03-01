<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/layout.php';

$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    if ($acao === 'lancar_mensalidade') {
        $stmt = $pdo->prepare('INSERT INTO mensalidades (aluno_id, referencia, valor, pago) VALUES (:aluno_id, :referencia, :valor, :pago)');
        $stmt->execute([
            ':aluno_id' => (int) ($_POST['aluno_id'] ?? 0),
            ':referencia' => trim($_POST['referencia'] ?? ''),
            ':valor' => (float) ($_POST['valor'] ?? 0),
            ':pago' => isset($_POST['pago']) ? 1 : 0,
        ]);

        header('Location: mensalidades.php?ok=1');
        exit;
    }

    if ($acao === 'marcar_pago') {
        $stmt = $pdo->prepare('UPDATE mensalidades SET pago = 1 WHERE id = :id');
        $stmt->execute([':id' => (int) ($_POST['mensalidade_id'] ?? 0)]);

        header('Location: mensalidades.php?ok=2');
        exit;
    }
}

$alunos = $pdo->query('SELECT al.id, al.nome, a.nome AS academia_nome FROM alunos al JOIN academias a ON a.id = al.academia_id ORDER BY al.nome')->fetchAll(PDO::FETCH_ASSOC);
$mensalidades = $pdo->query('SELECT m.*, al.nome AS aluno_nome FROM mensalidades m JOIN alunos al ON al.id = m.aluno_id ORDER BY m.referencia DESC, m.id DESC')->fetchAll(PDO::FETCH_ASSOC);

renderHeader('Mensalidades');
?>
<?php if (($_GET['ok'] ?? '') === '1'): ?>
    <div class="alerta">Mensalidade lançada com sucesso.</div>
<?php endif; ?>
<?php if (($_GET['ok'] ?? '') === '2'): ?>
    <div class="alerta">Mensalidade marcada como paga.</div>
<?php endif; ?>

<section class="bloco">
    <h2>Lançar Mensalidade</h2>
    <?php if (count($alunos) === 0): ?>
        <p>Cadastre ao menos um aluno antes de lançar mensalidades.</p>
    <?php else: ?>
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
    <?php endif; ?>
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
<?php
renderFooter();
