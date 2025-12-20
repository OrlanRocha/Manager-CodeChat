# CodeChat Manager

Sistema de gerenciamento para instâncias da CodeChat API (Baileys) com arquitetura MVC leve em PHP 8.2+.

## Requisitos

- PHP 8.2+
- MariaDB/MySQL
- Apache com `mod_rewrite` habilitado

## Estrutura do Projeto

```
/app
  /Controllers
  /Models
  /Core
/config
/public
  /assets
```

## Configuração

1. Crie o banco e tabelas usando o script:

```sql
-- arquivo database.sql
```

2. Ajuste as credenciais e URL da API em `config/config.php`.

## Uso

1. Aponte o DocumentRoot do Apache para a pasta `/public`.
2. Garanta que o `.htaccess` está sendo carregado pelo Apache.
3. Acesse o projeto via navegador (ex.: `http://localhost`).

## Desenvolvimento

- As rotas são registradas via `App\Core\Router`.
- A conexão com o banco usa o singleton `App\Core\Database`.

## Scripts

- `database.sql` cria as tabelas `users` e `instances`.

## Licença

Distribuído sob a licença MIT. Veja `LICENSE`.
