# 🔐 Auth Service

> Serviço de autenticação reutilizável — construído com Laravel 13 e Laravel Sanctum.

## 📋 Sobre o projeto

O **Auth Service** é um microserviço de autenticação independente que centraliza o gerenciamento de usuários e tokens. Outros projetos consomem este serviço para autenticar requisições sem precisar implementar autenticação própria.

O projeto foi desenvolvido como portfólio para demonstrar conhecimentos em arquitetura de microsserviços, autenticação via token com Laravel Sanctum e boas práticas de desenvolvimento back-end com Laravel.

## ✨ Funcionalidades

| Ação | Descrição |
|---|---|
| **Registrar usuário** | Cadastra um novo usuário com nome, email e senha |
| **Login** | Autentica o usuário e retorna um token de acesso |
| **Logout** | Invalida o token atual do usuário |
| **Dados do usuário** | Retorna os dados do usuário autenticado |
| **Validar token** | Verifica se um token é válido — usado por outros serviços |

## 🛠️ Tecnologias utilizadas

- **PHP 8.5** + **Laravel 13**
- **MySQL** — banco de dados relacional
- **Laravel Sanctum** — autenticação via token
- **Eloquent ORM** — mapeamento objeto-relacional
- **Form Request** — validação de dados separada por operação

## 🏗️ Arquitetura

```
app/
├── Http/
│   ├── Controllers/
│   │   └── AuthController.php      # Registro, login, logout, me e validateToken
│   └── Requests/
│       ├── RegisterRequest.php     # Validação do cadastro
│       └── LoginRequest.php        # Validação do login
└── Models/
    └── User.php                    # Model com HasApiTokens

routes/
└── api.php                         # Rotas públicas e protegidas
```

**Fluxo de autenticação:**

```
POST /api/register → Cria usuário → Retorna dados

POST /api/login → Valida credenciais → Gera token → Retorna token

GET /api/me → Valida token → Retorna usuário

GET /api/validate-token → Valida token → Retorna { token_valido: true }

POST /api/logout → Invalida token → Retorna confirmação
```

**Como outros projetos consomem este serviço:**

```
market-list-api
      ↓
Recebe requisição com token Bearer
      ↓
Chama GET /api/validate-token no Auth Service
      ↓
Token válido → executa a ação
Token inválido → retorna 401
```

## 🧠 Decisões técnicas

### Por que um serviço separado?
Centralizar a autenticação em um serviço independente evita duplicação de código entre projetos. Qualquer API que precise de autenticação pode consumir este serviço sem implementar login próprio — é o princípio DRY aplicado em nível de arquitetura.

### Por que Laravel Sanctum?
O Sanctum é a solução oficial do Laravel para autenticação via token em APIs. É simples, seguro e já vem integrado ao ecossistema Laravel. Cada token é salvo no banco e pode ser invalidado individualmente no logout.

### Por que a rota `/validate-token`?
Outros projetos precisam verificar se um token é válido antes de executar ações protegidas. O middleware `auth:sanctum` faz essa verificação automaticamente — se o token chegou até o método `validateToken()`, ele já é válido.

### Por que Form Requests separados para register e login?
As regras são diferentes. No registro, `password` requer confirmação e mínimo de 8 caracteres. No login, só verifica se foi enviado — a validação real acontece no `Auth::attempt()`.

## 🚀 Como rodar localmente

### Pré-requisitos

- PHP 8.3+
- Composer
- MySQL

### Instalação

```bash
# 1. Clone o repositório
git clone https://github.com/felipekauan1/auth-service.git
cd auth-service

# 2. Instale as dependências
composer install

# 3. Configure o ambiente
cp .env.example .env
php artisan key:generate

# 4. Configure o banco de dados no .env
DB_DATABASE=auth_service
DB_USERNAME=root
DB_PASSWORD=sua_senha

# 5. Crie o banco e rode as migrations
php artisan migrate
```

### Rodando o projeto

```bash
php artisan serve
```

O serviço estará disponível em `http://localhost:8000`.

## 📡 Endpoints da API

### Rotas públicas

#### Registrar usuário
```
POST /api/register
Content-Type: application/json

{
    "name": "Felipe",
    "email": "felipe@email.com",
    "password": "12345678",
    "password_confirmation": "12345678"
}
```

**Resposta (201):**
```json
{
    "sucesso": true,
    "mensagem": "Usuário registrado com sucesso!",
    "usuario": {
        "id": 1,
        "name": "Felipe",
        "email": "felipe@email.com",
        "created_at": "2026-06-01T..."
    }
}
```

#### Login
```
POST /api/login
Content-Type: application/json

{
    "email": "felipe@email.com",
    "password": "12345678"
}
```

**Resposta (200):**
```json
{
    "sucesso": true,
    "mensagem": "Usuário logado com sucesso!",
    "token": "1|abc123..."
}
```

### Rotas protegidas

> Todas exigem o header: `Authorization: Bearer {token}`

#### Dados do usuário autenticado
```
GET /api/me
```

**Resposta (200):**
```json
{
    "sucesso": true,
    "usuario": {
        "id": 1,
        "name": "Felipe",
        "email": "felipe@email.com"
    }
}
```

#### Validar token
```
GET /api/validate-token
```

**Resposta (200):**
```json
{
    "token_valido": true
}
```

**Token inválido (401):**
```json
{
    "message": "Unauthenticated."
}
```

#### Logout
```
POST /api/logout
```

**Resposta (200):**
```json
{
    "sucesso": true,
    "mensagem": "Logout realizado com sucesso!"
}
```

## 🔗 Projetos que consomem este serviço

| Projeto | Repositório |
|---|---|
| **Market List API** | [felipekauan1/market-list-api](https://github.com/felipekauan1/market-list-api) |

## 📌 Possíveis melhorias futuras

- Refresh token para renovação sem novo login
- Revogação de todos os tokens do usuário
- Rate limiting nas rotas de login e registro
- Testes automatizados com PHPUnit

## 👨‍💻 Autor

Desenvolvido por **[@felipekauan1](https://github.com/felipekauan1)**

## 📄 Licença

Este projeto está sob a licença MIT.
