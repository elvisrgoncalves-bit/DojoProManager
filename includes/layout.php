<?php

declare(strict_types=1);

function renderHeader(string $titulo): void
{
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo htmlspecialchars($titulo); ?> - DojoPro Manager</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <div class="wrapper">
            <header>
                <h1>DojoPro Manager</h1>
                <p>Sistema legado de gestão para academias de artes marciais</p>
            </header>

            <nav class="menu-legado">
                <a href="index.php">Painel</a>
                <a href="academias.php">Academias</a>
                <a href="instrutores.php">Instrutores</a>
                <a href="alunos.php">Alunos</a>
                <a href="mensalidades.php">Mensalidades</a>
            </nav>
    <?php
}

function renderFooter(): void
{
    ?>
        </div>
    </body>
    </html>
    <?php
}
