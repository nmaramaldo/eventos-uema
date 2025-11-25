# Plataforma de Eventos UEMA (EVENTOS-UEMA)

![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php)
![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap)
![SQLite](https://img.shields.io/badge/SQLite-3.x-003B57?style=for-the-badge&logo=sqlite)

**Status do Projeto:** `Em Desenvolvimento Ativo (Versão 1.0)`

Este projeto é uma plataforma web robusta para o cadastro, gerenciamento e divulgação de eventos acadêmicos, científicos e culturais da **Universidade Estadual do Maranhão (UEMA)**.

O sistema está sendo desenvolvido como parte do programa de **Residência Tecnológica da BRISA** em parceria com a UEMA, com o objetivo de centralizar e profissionalizar todo o ciclo de vida dos eventos da universidade.

## 🚀 Funcionalidades Principais

### Gerenciamento de Eventos
* **Fluxo de Criação em 3 Passos:** Um fluxo intuitivo para o organizador cadastrar (1) Informações Gerais, (2) Programação e (3) Palestrantes, salvando os dados na sessão entre as etapas.
* **Gerenciamento Completo (CRUD):** Funcionalidades completas para listar, editar e excluir eventos.
* **Publicação de Eventos:** Um evento só pode ser "Publicado" (visível ao público) após ter sua programação cadastrada, garantindo a integridade dos dados.
* **Pagamento:** Opção de cadastro de eventos "Grátis", "Pix" ou "Outros", com campo dinâmico para detalhes.

### Gerenciamento de Programação e Palestrantes
* **CRUD Aninhado:** Sistema robusto onde cada evento possui seu próprio gerenciamento de programação e palestrantes.
* **Salvar Atividades com AJAX:** A tela de "Programação" permite adicionar e salvar cada atividade (palestra, workshop) individualmente, sem recarregar a página.
* **Associação:** Palestrantes podem ser criados e associados a múltiplos eventos e atividades.

### Controle de Acesso e Usuários
* **Controle de Acesso por Papel (ACL):** Sistema de permissões baseado em `Policies` do Laravel.
    * **Master:** Controle total do sistema, incluindo gerenciamento de usuários.
    * **Admin:** Gerencia todos os aspectos dos eventos.
    * **Usuário Comum (Participante):** Pode se inscrever em eventos e gerenciar suas inscrições.
* **Validação de CPF:** O formulário de registro de novos usuários inclui máscara (`000.000.000-00`), validação em tempo real (JavaScript) e no back-end (`laravel/validador`).
* **Gerenciamento de Inscrições:** O usuário pode cancelar a própria inscrição. Admins podem gerenciar todas.

### Módulo de Relatórios
* **Relatório Mestre-Detalhe:** Página principal de relatórios com filtros de busca (nome, período).
* **Relatório Detalhado:** Ao clicar em um evento, o admin vê um relatório individual com a lista de todos os participantes inscritos e seus status de check-in.
* **Exportação para PDF:** Funcionalidade de exportação de relatórios detalhados para PDF, incluindo:
    * Logo oficial da UEMA.
    * Cabeçalho com o nome do usuário que exportou, data e hora.

---

## 🛠️ Arquitetura e Tecnologias

Este projeto utiliza uma arquitetura **Laravel (back-end)** tradicional, com a interface renderizada pelo **Blade (front-end)**.

* **Back-end:**
    * PHP 8.2
    * Laravel 12
    * Arquitetura **Model-View-Controller (MVC)**
    * Autorização via **Policies** (`EventPolicy`, `InscricaoPolicy`, `UserPolicy`)
* **Front-end:**
    * Laravel **Blade**
    * **Bootstrap 5** (via CDN)
    * JavaScript (para interatividade, como formulários dinâmicos e validação de CPF)
* **Banco de Dados:**
    * Desenvolvimento: **SQLite**
    * Produção (Planejado): **PostgreSQL**
* **Pacotes Principais:**
    * `barryvdh/laravel-dompdf` (para geração de PDFs)
    * `laravel/validador` (para validação de CPF)

---

## 📦 Instalação (Ambiente de Desenvolvimento)

Siga estes passos para configurar o projeto localmente.

### 1. Pré-requisitos

* PHP >= 8.2
* Composer
* Extensão PHP para `gd` (necessária para a geração de PDF com imagens)
* Extensão PHP para `sqlite3`

### 2. Passos

1. Clone o repositório: `[https://github.com/nmaramaldo/eventos-uema.git](https://github.com/nmaramaldo/eventos-uema.git)`
`cd eventos-uema`
2. Instale as dependências: `npm install` 
3. Configure o `.env`: `cp .env.example .env` 
4. Crie o arquivo do banco: `touch database/database.sqlite` 
5. Rode as migrations e seeders: `php artisan migrate` 
6. Inicie o servidor: `php artisan serve`




