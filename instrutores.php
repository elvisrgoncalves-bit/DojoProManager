<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/layout.php';

$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare('INSERT INTO instrutores (academia_id, nome, graduacao, email) VALUES (:academia_id, :nome, :graduacao, :email)');
    $stmt->execute([
        ':academia_id' => (int) ($_POST['academia_instrutor'] ?? 0),
        ':nome' => trim($_POST['nome_instrutor'] ?? ''),
        ':graduacao' => trim($_POST['graduacao'] ?? ''),
        ':email' => trim($_POST['email_instrutor'] ?? ''),
    ]);

    header('Location: instrutores.php?ok=1');
    exit;
}

$academias = $pdo->query('SELECT id, nome FROM academias ORDER BY nome')->fetchAll(PDO::FETCH_ASSOC);
$instrutores = $pdo->query('SELECT i.*, a.nome AS academia_nome FROM instrutores i JOIN academias a ON a.id = i.academia_id ORDER BY i.nome')->fetchAll(PDO::FETCH_ASSOC);

renderHeader('Instrutores');
?>
<?php if (isset($_GET['ok'])): ?>
    <div class="alerta">Instrutor cadastrado com sucesso.</div>
<?php endif; ?>

<section class="bloco">
    <h2>Cadastro de Instrutor</h2>
    <?php if (count($academias) === 0): ?>
        <p>Cadastre ao menos uma academia antes de incluir instrutores.</p>
    <?php else: ?>
        <form method="post" class="form-legado">
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
    <?php endif; ?>
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
<?php
renderFooter();
