# ERP Fone Ninja - Desafio Técnico

Este é um sistema de Gestão de Recursos (ERP) desenvolvido como desafio técnico, focado no controle de estoque, compras e vendas de produtos.

O sistema foi construído priorizando a **precisão financeira** (Cálculo de Custo Médio e Lucro) e uma **experiência de usuário (UX)**.

---

## 🛠️ Tecnologias Utilizadas

### Backend

- **Laravel 12** (PHP 8.3)
- **Laravel Sanctum** (Autenticação Stateless)
- **Laravel Octane + FrankenPHP** (Estrutura)
- **MySQL 8** (Banco de dados relacional)

### Frontend

- **Vue 3** (Composition API)
- **Vite** (Build tool)
- **Tailwind CSS** (Estilização)
- **Heroicons** (Ícones SVG)

---

## 🚀 Como Rodar o Projeto

O projeto é totalmente containerizado. Para rodar, você só precisa ter o **Docker** e o **Docker Compose** instalados.

1. Clone o repositório.
2. Na raiz do projeto, execute:
   ```bash
   docker-compose up -d --build
   ```
3. O sistema estará disponível em:
   - **Frontend**: [http://localhost:5173](http://localhost:5173)
   - **Backend API**: [http://localhost:8080](http://localhost:8080)

> **Nota:** No primeiro boot, o sistema automaticamente roda as migrations e popula o banco de dados com dados iniciais (Seeders).

### 🔑 Credenciais Padrão

- **Usuário**: `admin@fone-ninja.com`
- **Senha**: `password`

_(Você também pode criar uma nova conta através da tela de Registro)_

---
