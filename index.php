<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/layout.php';

$pdo = db();

$totalAlunos = (int) $pdo->query('SELECT COUNT(*) FROM alunos')->fetchColumn();
$totalInstrutores = (int) $pdo->query('SELECT COUNT(*) FROM instrutores')->fetchColumn();
$totalAcademias = (int) $pdo->query('SELECT COUNT(*) FROM academias')->fetchColumn();
$totalEmAberto = (float) $pdo->query('SELECT COALESCE(SUM(valor), 0) FROM mensalidades WHERE pago = 0')->fetchColumn();

$ultimasAcademias = $pdo->query('SELECT nome, cidade, estilo_principal, criado_em FROM academias ORDER BY id DESC LIMIT 10')->fetchAll(PDO::FETCH_ASSOC);
$ultimasMensalidades = $pdo->query('SELECT m.referencia, m.valor, m.pago, a.nome AS aluno_nome FROM mensalidades m JOIN alunos a ON a.id = m.aluno_id ORDER BY m.id DESC LIMIT 10')->fetchAll(PDO::FETCH_ASSOC);

renderHeader('Painel');
?>
<section class="painel-resumo">
    <div><strong>Academias:</strong> <?php echo $totalAcademias; ?></div>
    <div><strong>Instrutores:</strong> <?php echo $totalInstrutores; ?></div>
    <div><strong>Alunos:</strong> <?php echo $totalAlunos; ?></div>
    <div><strong>Mensalidades em aberto:</strong> R$ <?php echo number_format($totalEmAberto, 2, ',', '.'); ?></div>
</section>

<section class="bloco atalhos">
    <h2>Atalhos de Cadastro</h2>
    <p>Agora cada formulário fica em uma página separada:</p>
    <ul>
        <li><a href="academias.php">Cadastro de Academia</a></li>
        <li><a href="instrutores.php">Cadastro de Instrutor</a></li>
        <li><a href="alunos.php">Cadastro de Aluno</a></li>
        <li><a href="mensalidades.php">Lançar e controlar Mensalidades</a></li>
    </ul>
</section>

<section class="bloco">
    <h2>Últimas Academias</h2>
    <table>
        <thead>
            <tr><th>Nome</th><th>Cidade</th><th>Estilo</th><th>Cadastro</th></tr>
        </thead>
        <tbody>
            <?php foreach ($ultimasAcademias as $academia): ?>
                <tr>
                    <td><?php echo htmlspecialchars($academia['nome']); ?></td>
                    <td><?php echo htmlspecialchars($academia['cidade']); ?></td>
                    <td><?php echo htmlspecialchars($academia['estilo_principal']); ?></td>
                    <td><?php echo htmlspecialchars($academia['criado_em']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

<section class="bloco">
    <h2>Últimas Mensalidades</h2>
    <table>
        <thead>
            <tr><th>Aluno</th><th>Referência</th><th>Valor</th><th>Status</th></tr>
        </thead>
        <tbody>
            <?php foreach ($ultimasMensalidades as $mensalidade): ?>
                <tr>
                    <td><?php echo htmlspecialchars($mensalidade['aluno_nome']); ?></td>
                    <td><?php echo htmlspecialchars($mensalidade['referencia']); ?></td>
                    <td>R$ <?php echo number_format((float) $mensalidade['valor'], 2, ',', '.'); ?></td>
                    <td><?php echo (int) $mensalidade['pago'] === 1 ? 'Pago' : 'Em aberto'; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>
<?php
renderFooter();
