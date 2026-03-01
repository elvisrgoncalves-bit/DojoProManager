<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/layout.php';

$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare('INSERT INTO alunos (academia_id, nome, faixa, idade, status_matricula) VALUES (:academia_id, :nome, :faixa, :idade, :status)');
    $stmt->execute([
        ':academia_id' => (int) ($_POST['academia_aluno'] ?? 0),
        ':nome' => trim($_POST['nome_aluno'] ?? ''),
        ':faixa' => trim($_POST['faixa'] ?? ''),
        ':idade' => (int) ($_POST['idade'] ?? 0),
        ':status' => trim($_POST['status_matricula'] ?? 'Ativo'),
    ]);

    header('Location: alunos.php?ok=1');
    exit;
}

$academias = $pdo->query('SELECT id, nome FROM academias ORDER BY nome')->fetchAll(PDO::FETCH_ASSOC);
$alunos = $pdo->query('SELECT al.*, a.nome AS academia_nome FROM alunos al JOIN academias a ON a.id = al.academia_id ORDER BY al.nome')->fetchAll(PDO::FETCH_ASSOC);

renderHeader('Alunos');
?>
<?php if (isset($_GET['ok'])): ?>
    <div class="alerta">Aluno cadastrado com sucesso.</div>
<?php endif; ?>

<section class="bloco">
    <h2>Cadastro de Aluno</h2>
    <?php if (count($academias) === 0): ?>
        <p>Cadastre ao menos uma academia antes de incluir alunos.</p>
    <?php else: ?>
        <form method="post" class="form-legado">
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
    <?php endif; ?>
</section>

<section class="bloco">
    <h2>Alunos Cadastrados</h2>
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
<?php
renderFooter();
