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

1. Acesse `/install` para executar o assistente interativo:
   - Conectar ao MySQL
   - Criar banco
   - Gerar `.env`
   - Criar tabelas
   - Importar seeds (opcional)

## Uso

1. Aponte o DocumentRoot do Apache para a pasta `/public`.
2. Garanta que o `.htaccess` está sendo carregado pelo Apache.
3. Acesse o projeto via navegador (ex.: `http://localhost`).
4. Caso não exista `.env` ou o banco esteja indisponível, o sistema redireciona para `/install`.
5. Acompanhe logs internos em `/logs` e em `storage/logs/app.log`.
6. Acesse `/myprofile` para atualizar dados da conta e senha.
7. Acesse `/user` para gerenciar usuários (apenas admin).

### Credenciais padrão (seeds)

- **E-mail:** `admin@codechat.local`
- **Senha:** `admin123`

## Desenvolvimento

- As rotas são registradas via `App\Core\Router`.
- A conexão com o banco usa o singleton `App\Core\Database`.
- O assistente `/install` cria `.env` e prepara o banco automaticamente.
- As páginas de erro 404/500 ficam em `app/Views/errors`.
- A integração com CodeChat usa endpoints `/instance/create`, `/instance/fetchInstances` e `/instance/connectionState`.
- As rotas internas do painel usam `/instances` (sem prefixo `/api`).
- A chave global da API é configurada no `.env` como `API_KEY` (definida no assistente de instalação).
- O JWT global opcional é configurado no `.env` como `API_JWT` (definido no assistente).
- O token JWT de cada instância é salvo em `instances.api_key` após o `POST /instance/create` e usado nas chamadas seguintes (connect/status/send).
- Em respostas HTTP 401, revise a `API_KEY` global e o JWT da instância.
- O campo `api_key` não é exposto na listagem de instâncias por segurança.
- Cada instância é vinculada ao usuário criador e só é visível para ele (admin visualiza todas).
- O logger grava eventos no banco (tabela `logs`) e em arquivo (`storage/logs/app.log`).
- A tela de usuários permite criar, editar, bloquear e excluir contas.

## Scripts

- `database.sql` cria as tabelas `users` e `instances`.
- `seeds.sql` cria usuário admin e instâncias de exemplo.
- A tabela `logs` é criada junto ao schema principal.

## Licença

Distribuído sob a licença MIT. Veja `LICENSE`.
