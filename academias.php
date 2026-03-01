<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/layout.php';

$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare('INSERT INTO academias (nome, cidade, estilo_principal, telefone, criado_em) VALUES (:nome, :cidade, :estilo, :telefone, :criado_em)');
    $stmt->execute([
        ':nome' => trim($_POST['nome_academia'] ?? ''),
        ':cidade' => trim($_POST['cidade'] ?? ''),
        ':estilo' => trim($_POST['estilo_principal'] ?? ''),
        ':telefone' => trim($_POST['telefone'] ?? ''),
        ':criado_em' => date('Y-m-d H:i:s'),
    ]);

    header('Location: academias.php?ok=1');
    exit;
}

$academias = $pdo->query('SELECT * FROM academias ORDER BY nome')->fetchAll(PDO::FETCH_ASSOC);

renderHeader('Academias');
?>
<?php if (isset($_GET['ok'])): ?>
    <div class="alerta">Academia cadastrada com sucesso.</div>
<?php endif; ?>

<section class="bloco">
    <h2>Cadastro de Academia</h2>
    <form method="post" class="form-legado">
        <label>Nome da academia <input name="nome_academia" required></label>
        <label>Cidade <input name="cidade" required></label>
        <label>Estilo principal <input name="estilo_principal" placeholder="Karatê, Jiu-Jitsu..." required></label>
        <label>Telefone <input name="telefone"></label>
        <button type="submit">Cadastrar</button>
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
<?php
renderFooter();
