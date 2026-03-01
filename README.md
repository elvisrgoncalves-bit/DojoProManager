# DojoProManager

Aplicação em **PHP legado + HTML/CSS** para gestão de academias de artes marciais.

## Funcionalidades

- Painel inicial com indicadores.
- Formulários separados por página:
  - `academias.php`
  - `instrutores.php`
  - `alunos.php`
  - `mensalidades.php`
- Cadastro de academias, instrutores e alunos.
- Lançamento e controle de mensalidades.

## Requisitos

- PHP 8.1+ com extensão `pdo_sqlite` habilitada.

## Como executar

```bash
php -S 0.0.0.0:8000
```

Depois, abra no navegador:

```text
http://localhost:8000
```

O banco SQLite é criado automaticamente em `data/dojo.db` no primeiro acesso.
