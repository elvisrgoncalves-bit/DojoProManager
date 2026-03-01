<?php

declare(strict_types=1);

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $databaseDir = __DIR__ . '/../data';
    if (!is_dir($databaseDir)) {
        mkdir($databaseDir, 0777, true);
    }

    $pdo = new PDO('sqlite:' . $databaseDir . '/dojo.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec('PRAGMA foreign_keys = ON');

    createSchema($pdo);

    return $pdo;
}

function createSchema(PDO $pdo): void
{
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS academias (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nome TEXT NOT NULL,
            cidade TEXT NOT NULL,
            estilo_principal TEXT NOT NULL,
            telefone TEXT,
            criado_em TEXT NOT NULL
        )'
    );

    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS instrutores (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            academia_id INTEGER NOT NULL,
            nome TEXT NOT NULL,
            graduacao TEXT NOT NULL,
            email TEXT,
            FOREIGN KEY (academia_id) REFERENCES academias (id) ON DELETE CASCADE
        )'
    );

    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS alunos (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            academia_id INTEGER NOT NULL,
            nome TEXT NOT NULL,
            faixa TEXT NOT NULL,
            idade INTEGER NOT NULL,
            status_matricula TEXT NOT NULL,
            FOREIGN KEY (academia_id) REFERENCES academias (id) ON DELETE CASCADE
        )'
    );

    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS mensalidades (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            aluno_id INTEGER NOT NULL,
            referencia TEXT NOT NULL,
            valor REAL NOT NULL,
            pago INTEGER NOT NULL DEFAULT 0,
            FOREIGN KEY (aluno_id) REFERENCES alunos (id) ON DELETE CASCADE
        )'
    );
}
