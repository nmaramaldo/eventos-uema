@extends('layouts.app')

@section('title', 'Documentação do Sistema - Eventos UEMA')

@section('content')
<div class="container py-5">
    <!-- Cabeçalho -->
    <div class="text-center mb-5">
        <h1 class="display-5 fw-bold text-primary">
            <i class="bi bi-book me-2"></i>Documentação do Sistema
        </h1>
        <p class="lead text-muted">
            Sistema de Gerenciamento de Eventos Acadêmicos - Eventos UEMA
        </p>
        <div class="badge bg-info fs-6 mb-3">Versão 1.0</div>
    </div>

    <!-- Índice Interativo -->
    <div class="card shadow-lg mb-5">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="bi bi-list-ol me-2"></i>Sumário Executivo</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#introducao" class="text-decoration-none text-primary fw-medium"><i class="bi bi-chevron-right me-2"></i>1. Introdução</a></li>
                        <li class="mb-2"><a href="#tecnologias" class="text-decoration-none text-primary fw-medium"><i class="bi bi-chevron-right me-2"></i>3. Tecnologias Utilizadas</a></li>
                        <li class="mb-2"><a href="#requisitos" class="text-decoration-none text-primary fw-medium"><i class="bi bi-chevron-right me-2"></i>4. Análise de Requisitos</a></li>
                        <li class="mb-2"><a href="#modelagem" class="text-decoration-none text-primary fw-medium"><i class="bi bi-chevron-right me-2"></i>5. Modelagem do Sistema</a></li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#funcionalidades" class="text-decoration-none text-primary fw-medium"><i class="bi bi-chevron-right me-2"></i>6. Funcionalidades</a></li>
                        <li class="mb-2"><a href="#instalacao" class="text-decoration-none text-primary fw-medium"><i class="bi bi-chevron-right me-2"></i>7. Guia de Instalação</a></li>
                        <li class="mb-2"><a href="#manual" class="text-decoration-none text-primary fw-medium"><i class="bi bi-chevron-right me-2"></i>8. Manual do Usuário</a></li>
                        <li class="mb-2"><a href="#conclusao" class="text-decoration-none text-primary fw-medium"><i class="bi bi-chevron-right me-2"></i>9. Considerações Finais</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Seções de Conteúdo -->
    <div class="documentation-content">
        
        <!-- Seção 1: Introdução -->
        <section id="introducao" class="mb-5">
            <div class="d-flex align-items-center mb-4">
                <span class="badge bg-primary fs-5 me-3">1</span>
                <h2 class="mb-0">Introdução</h2>
            </div>
            
            <div class="card mb-4">
                <div class="card-body">
                    <p class="card-text">
                        O <strong class="text-primary">Sistema Eventos UEMA</strong> é uma aplicação web desenvolvida para o gerenciamento de eventos acadêmicos. 
                        A solução abrange funcionalidades essenciais como cadastro de eventos, gerenciamento de usuários, 
                        realização de inscrições, controle de presença e emissão de relatórios gerenciais.
                    </p>
                </div>
            </div>

            <!-- Objetivo -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card h-100 border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="bi bi-bullseye me-2"></i>1.1 Objetivo</h5>
                        </div>
                        <div class="card-body">
                            <p>Centralizar e automatizar o processo de organização de eventos, oferecendo uma ferramenta eficiente para administradores e organizadores, ao mesmo tempo que proporciona uma experiência simplificada e intuitiva para os participantes.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100 border-info">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="bi bi-diagram-3 me-2"></i>1.2 Escopo</h5>
                        </div>
                        <div class="card-body">
                            <p>Contempla funcionalidades de gestão de eventos e gestão de usuários (cadastro, autenticação e perfis). <span class="text-danger">Não inclui</span> processamento de pagamentos online por decisão de projeto.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Público-Alvo -->
            <div class="card border-warning">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="bi bi-people me-2"></i>1.3 Público-Alvo</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="d-flex align-items-start mb-3">
                                <i class="bi bi-person-gear fs-3 text-primary me-3"></i>
                                <div>
                                    <h6 class="fw-bold">Administradores e Organizadores</h6>
                                    <p class="mb-0 text-muted">Responsáveis pela gestão completa do sistema, incluindo cadastro de eventos, controle de inscrições e geração de relatórios.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start mb-3">
                                <i class="bi bi-mortarboard fs-3 text-success me-3"></i>
                                <div>
                                    <h6 class="fw-bold">Participantes</h6>
                                    <p class="mb-0 text-muted">Usuários que interagem com o sistema para se inscreverem em eventos e acompanharem suas informações de participação.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Seção 3: Tecnologias -->
        <section id="tecnologias" class="mb-5">
            <div class="d-flex align-items-center mb-4">
                <span class="badge bg-success fs-5 me-3">3</span>
                <h2 class="mb-0">Tecnologias Utilizadas</h2>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Categoria</th>
                            <th>Tecnologia</th>
                            <th>Versão/Detalhe</th>
                            <th>Função no Sistema</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="badge bg-primary">Backend</span></td>
                            <td><i class="bi bi-filetype-php text-primary me-2"></i>PHP</td>
                            <td>8.2</td>
                            <td>Lógica de backend e processamento de requisições</td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-danger">Framework</span></td>
                            <td><i class="bi bi-laravel text-danger me-2"></i>Laravel</td>
                            <td>10</td>
                            <td>Estrutura MVC, roteamento, ORM e segurança</td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-info">Frontend</span></td>
                            <td><i class="bi bi-filetype-html text-danger me-2"></i>HTML5/CSS3/Blade</td>
                            <td>Bootstrap 5</td>
                            <td>Interface do usuário e responsividade</td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-warning">Banco de Dados</span></td>
                            <td><i class="bi bi-database text-warning me-2"></i>SQLite</td>
                            <td>Ambiente de Dev</td>
                            <td>Persistência e gerenciamento dos dados</td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-secondary">PDF</span></td>
                            <td><i class="bi bi-file-earmark-pdf text-danger me-2"></i>laravel-dompdf</td>
                            <td>-</td>
                            <td>Geração de relatórios em formato PDF</td>
                        </tr>
                        <tr>
                            <td><span class="badge bg-dark">Controle de Versão</span></td>
                            <td><i class="bi bi-git text-dark me-2"></i>Git e GitHub</td>
                            <td>-</td>
                            <td>Rastreamento e colaboração no código-fonte</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Seção 4: Requisitos -->
        <section id="requisitos" class="mb-5">
            <div class="d-flex align-items-center mb-4">
                <span class="badge bg-info fs-5 me-3">4</span>
                <h2 class="mb-0">Análise de Requisitos</h2>
            </div>

            <!-- Requisitos Funcionais -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-check2-square me-2"></i>4.1 Requisitos Funcionais (RF)</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach([
                            ['num' => 'RF01', 'desc' => 'Cadastro e autenticação segura de novos usuários'],
                            ['num' => 'RF02', 'desc' => 'Gerenciamento de perfis de acesso (Master, Admin e Participante)'],
                            ['num' => 'RF03', 'desc' => 'Cadastro, edição e exclusão de eventos por usuários autorizados'],
                            ['num' => 'RF04', 'desc' => 'Inscrição de usuários em eventos disponíveis'],
                            ['num' => 'RF05', 'desc' => 'Controle de presença dos participantes nos eventos'],
                            ['num' => 'RF06', 'desc' => 'Geração de relatórios gerenciais em formato PDF'],
                            ['num' => 'RF07', 'desc' => 'Emissão de certificados digitais para os participantes']
                        ] as $rf)
                        <div class="col-md-6 mb-2">
                            <div class="d-flex">
                                <span class="badge bg-success me-2">{{ $rf['num'] }}</span>
                                <span>{{ $rf['desc'] }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Requisitos Não Funcionais -->
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="bi bi-shield-check me-2"></i>4.2 Requisitos Não Funcionais (RNF)</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach([
                            ['num' => 'RNF01', 'tipo' => 'Usabilidade', 'desc' => 'Sistema acessível via navegador web'],
                            ['num' => 'RNF02', 'tipo' => 'Segurança', 'desc' => 'Mecanismo de autenticação seguro'],
                            ['num' => 'RNF03', 'tipo' => 'Arquitetura', 'desc' => 'Utilização da arquitetura MVC'],
                            ['num' => 'RNF04', 'tipo' => 'Desempenho', 'desc' => 'Tempo de resposta adequado']
                        ] as $rnf)
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <span class="badge bg-warning text-dark">{{ $rnf['num'] }}</span>
                                        <small class="text-muted">({{ $rnf['tipo'] }})</small>
                                    </h6>
                                    <p class="card-text mb-0">{{ $rnf['desc'] }}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Regras de Negócio -->
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="bi bi-gem me-2"></i>4.3 Regras de Negócio</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <i class="bi bi-slash-circle text-danger me-2"></i>
                            Apenas usuários com perfil <strong>Master</strong> ou <strong>Admin</strong> podem cadastrar novos eventos
                        </li>
                        <li class="list-group-item">
                            <i class="bi bi-person-check text-success me-2"></i>
                            Um participante pode realizar apenas <strong>uma única inscrição</strong> no mesmo evento
                        </li>
                        <li class="list-group-item">
                            <i class="bi bi-file-earmark-text text-primary me-2"></i>
                            Emissão de relatórios condicionada à existência de dados cadastrados
                        </li>
                        <li class="list-group-item">
                            <i class="bi bi-lock text-warning me-2"></i>
                            Usuários com perfil <strong>Participante</strong> não podem acessar funcionalidades administrativas
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- Seção 5: Modelagem -->
        <section id="modelagem" class="mb-5">
            <div class="d-flex align-items-center mb-4">
                <span class="badge bg-purple fs-5 me-3">5</span>
                <h2 class="mb-0">Modelagem do Sistema</h2>
            </div>

            <!-- Arquitetura MVC -->
            <div class="card mb-4">
                <div class="card-header bg-purple text-white">
                    <h5 class="mb-0"><i class="bi bi-diagram-3 me-2"></i>5.1 Arquitetura MVC</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="card border-primary h-100">
                                <div class="card-body">
                                    <i class="bi bi-database fs-1 text-primary mb-3"></i>
                                    <h5 class="card-title">Model</h5>
                                    <p class="card-text">Manipulação de dados e lógica de negócio via Eloquent ORM</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-success h-100">
                                <div class="card-body">
                                    <i class="bi bi-eye fs-1 text-success mb-3"></i>
                                    <h5 class="card-title">View</h5>
                                    <p class="card-text">Interface do usuário com Blade Templates</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-warning h-100">
                                <div class="card-body">
                                    <i class="bi bi-gear fs-1 text-warning mb-3"></i>
                                    <h5 class="card-title">Controller</h5>
                                    <p class="card-text">Intermediário entre Model e View</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Entidades -->
            <div class="accordion" id="accordionEntidades">
                <!-- Usuario -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseUsuario">
                            <i class="bi bi-person me-2"></i> Entidade: Usuario
                        </button>
                    </h2>
                    <div id="collapseUsuario" class="accordion-collapse collapse show" data-bs-parent="#accordionEntidades">
                        <div class="accordion-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Atributo</th>
                                            <th>Tipo</th>
                                            <th>Restrições</th>
                                            <th>Descrição</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr><td>id</td><td>INTEGER</td><td>PK, Auto-incremento</td><td>Identificador único</td></tr>
                                        <tr><td>nome</td><td>VARCHAR(255)</td><td>Não Nulo</td><td>Nome completo</td></tr>
                                        <tr><td>email</td><td>VARCHAR(255)</td><td>Não Nulo, Único</td><td>E-mail para autenticação</td></tr>
                                        <tr><td>senha</td><td>VARCHAR(255)</td><td>Não Nulo</td><td>Senha criptografada (Bcrypt)</td></tr>
                                        <tr><td>perfil</td><td>VARCHAR(50)</td><td>Não Nulo</td><td>Master, Admin ou Participante</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Evento -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEvento">
                            <i class="bi bi-calendar-event me-2"></i> Entidade: Evento
                        </button>
                    </h2>
                    <div id="collapseEvento" class="accordion-collapse collapse" data-bs-parent="#accordionEntidades">
                        <div class="accordion-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Atributo</th>
                                            <th>Tipo</th>
                                            <th>Restrições</th>
                                            <th>Descrição</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr><td>id</td><td>INTEGER</td><td>PK, Auto-incremento</td><td>Identificador único</td></tr>
                                        <tr><td>titulo</td><td>VARCHAR(255)</td><td>Não Nulo</td><td>Nome do evento</td></tr>
                                        <tr><td>descricao</td><td>TEXT</td><td>Não Nulo</td><td>Detalhes do evento</td></tr>
                                        <tr><td>data</td><td>DATE</td><td>Não Nulo</td><td>Data de realização</td></tr>
                                        <tr><td>local</td><td>VARCHAR(255)</td><td>Não Nulo</td><td>Local do evento</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Inscricao -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseInscricao">
                            <i class="bi bi-clipboard-check me-2"></i> Entidade: Inscricao
                        </button>
                    </h2>
                    <div id="collapseInscricao" class="accordion-collapse collapse" data-bs-parent="#accordionEntidades">
                        <div class="accordion-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Atributo</th>
                                            <th>Tipo</th>
                                            <th>Restrições</th>
                                            <th>Descrição</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr><td>id</td><td>INTEGER</td><td>PK, Auto-incremento</td><td>Identificador único</td></tr>
                                        <tr><td>id_usuario</td><td>INTEGER</td><td>FK para Usuario</td><td>Referência ao usuário</td></tr>
                                        <tr><td>id_evento</td><td>INTEGER</td><td>FK para Evento</td><td>Referência ao evento</td></tr>
                                        <tr><td>presenca</td><td>BOOLEAN</td><td>Padrão: FALSE</td><td>Status de presença</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Diagrama de Relacionamentos -->
            <div class="card mt-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-diagram-2 me-2"></i>5.2.4 Relacionamentos</h5>
                </div>
                <div class="card-body text-center">
                    <div class="d-flex justify-content-center align-items-center mb-4">
                        <div class="text-center mx-4">
                            <div class="p-3 border rounded bg-light">
                                <i class="bi bi-person fs-1 text-primary"></i>
                                <div class="mt-2">Usuario</div>
                                <small>(1)</small>
                            </div>
                        </div>
                        <div class="mx-3">
                            <i class="bi bi-arrow-left-right fs-3 text-muted"></i>
                        </div>
                        <div class="text-center mx-4">
                            <div class="p-3 border rounded bg-light">
                                <i class="bi bi-clipboard-check fs-1 text-success"></i>
                                <div class="mt-2">Inscrição</div>
                                <small>(N)</small>
                            </div>
                        </div>
                        <div class="mx-3">
                            <i class="bi bi-arrow-left-right fs-3 text-muted"></i>
                        </div>
                        <div class="text-center mx-4">
                            <div class="p-3 border rounded bg-light">
                                <i class="bi bi-calendar-event fs-1 text-warning"></i>
                                <div class="mt-2">Evento</div>
                                <small>(1)</small>
                            </div>
                        </div>
                    </div>
                    <p class="mb-0">
                        <strong>Relacionamento N:M:</strong> Um usuário pode ter múltiplas inscrições e um evento pode ter múltiplos inscritos
                    </p>
                </div>
            </div>
        </section>

        <!-- Seção 6: Funcionalidades -->
        <section id="funcionalidades" class="mb-5">
            <div class="d-flex align-items-center mb-4">
                <span class="badge bg-orange fs-5 me-3">6</span>
                <h2 class="mb-0">Funcionalidades do Sistema</h2>
            </div>

            <div class="row">
                @foreach([
                    ['icon' => 'bi-box-arrow-in-right', 'title' => 'Autenticação', 'desc' => 'Login seguro com controle de sessão e perfis de acesso'],
                    ['icon' => 'bi-calendar-plus', 'title' => 'Gestão de Eventos', 'desc' => 'Cadastro, edição e exclusão de eventos (Admin/Master)'],
                    ['icon' => 'bi-person-plus', 'title' => 'Inscrições', 'desc' => 'Participantes podem se inscrever em eventos disponíveis'],
                    ['icon' => 'bi-clipboard-check', 'title' => 'Controle de Presença', 'desc' => 'Registro e validação de presença nos eventos'],
                    ['icon' => 'bi-file-earmark-pdf', 'title' => 'Relatórios PDF', 'desc' => 'Geração de documentos gerenciais em formato PDF'],
                    ['icon' => 'bi-award', 'title' => 'Certificados Digitais', 'desc' => 'Emissão automática de certificados via DomPDF']
                ] as $func)
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <i class="bi {{ $func['icon'] }} fs-1 text-primary mb-3"></i>
                            <h5 class="card-title">{{ $func['title'] }}</h5>
                            <p class="card-text">{{ $func['desc'] }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Processo de Certificados -->
            <div class="card mt-3">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-award me-2"></i>6.4.1 Emissão de Certificados Digitais</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <ol class="list-group list-group-numbered">
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        <div class="fw-bold">Identificação</div>
                                        Sistema identifica participantes inscritos no evento
                                    </div>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        <div class="fw-bold">Recuperação de Dados</div>
                                        Coleta informações do participante e evento
                                    </div>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        <div class="fw-bold">Template Blade</div>
                                        Utiliza template para apresentação visual
                                    </div>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        <div class="fw-bold">Renderização</div>
                                        Laravel renderiza template com dados reais
                                    </div>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        <div class="fw-bold">Conversão PDF</div>
                                        DomPDF converte conteúdo para arquivo PDF
                                    </div>
                                </li>
                            </ol>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-success h-100">
                                <div class="card-body text-center">
                                    <i class="bi bi-file-earmark-pdf fs-1 text-success mb-3"></i>
                                    <h6>Certificado Digital</h6>
                                    <p class="small">Documento oficial de comprovação de participação</p>
                                    <div class="badge bg-success">Visualizar</div>
                                    <div class="badge bg-info">Salvar</div>
                                    <div class="badge bg-warning">Imprimir</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Seção 7: Instalação -->
        <section id="instalacao" class="mb-5">
            <div class="d-flex align-items-center mb-4">
                <span class="badge bg-dark fs-5 me-3">7</span>
                <h2 class="mb-0">Guia de Instalação e Configuração</h2>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="row">
                        @foreach([
                            ['step' => 1, 'title' => 'Clonar Repositório', 'desc' => 'Obtenha o código-fonte do projeto via Git'],
                            ['step' => 2, 'title' => 'Dependências PHP', 'desc' => 'Execute composer install para bibliotecas PHP'],
                            ['step' => 3, 'title' => 'Dependências Front-end', 'desc' => 'npm install para pacotes CSS/JS'],
                            ['step' => 4, 'title' => 'Arquivo .env', 'desc' => 'Configure variáveis de ambiente e banco de dados'],
                            ['step' => 5, 'title' => 'Configurar BD', 'desc' => 'Garanta que o banco esteja configurado'],
                            ['step' => 6, 'title' => 'Migrações', 'desc' => 'Execute php artisan migrate para criar tabelas'],
                            ['step' => 7, 'title' => 'Servidor Local', 'desc' => 'php artisan serve para iniciar aplicação']
                        ] as $step)
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <span class="badge bg-dark rounded-circle mb-3" style="width: 40px; height: 40px; line-height: 28px; font-size: 1.2rem;">{{ $step['step'] }}</span>
                                    <h6 class="card-title">{{ $step['title'] }}</h6>
                                    <p class="card-text small">{{ $step['desc'] }}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <!-- Seção 8: Manual -->
        <section id="manual" class="mb-5">
            <div class="d-flex align-items-center mb-4">
                <span class="badge bg-secondary fs-5 me-3">8</span>
                <h2 class="mb-0">Manual do Usuário</h2>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Para Administradores</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Acesse o sistema com credenciais de admin</li>
                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Utilize o painel de gestão para cadastrar eventos</li>
                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Controle inscrições e presenças</li>
                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Gere relatórios e certificados</li>
                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Gerencie usuários do sistema</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="bi bi-mortarboard me-2"></i>Para Participantes</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Faça login com suas credenciais</li>
                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Explore eventos disponíveis</li>
                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Realize inscrições nos eventos de interesse</li>
                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Acompanhe status de participação</li>
                                <li class="mb-2"><i class="bi bi-check-circle text-success me-2"></i>Visualize e baixe certificados</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Seção 9: Conclusão -->
        <section id="conclusao" class="mb-5">
            <div class="d-flex align-items-center mb-4">
                <span class="badge bg-danger fs-5 me-3">9</span>
                <h2 class="mb-0">Considerações Finais</h2>
            </div>

            <div class="card border-success">
                <div class="card-body">
                    <p class="card-text">
                        O <strong>Sistema Eventos UEMA</strong> cumpre seu objetivo de atender às necessidades básicas de 
                        gerenciamento de eventos acadêmicos, oferecendo uma solução organizada, segura e extensível, 
                        baseada em tecnologias de mercado como Laravel e o padrão MVC.
                    </p>
                    
                    <div class="mt-4">
                        <h5 class="text-primary"><i class="bi bi-rocket-takeoff me-2"></i>9.1 Futuras Melhorias</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="bi bi-envelope text-info me-2"></i>Implementação de notificações automáticas (e-mail ou push)</li>
                            <li class="mb-2"><i class="bi bi-credit-card text-success me-2"></i>Suporte completo a pagamentos online para eventos pagos</li>
                            <li class="mb-2"><i class="bi bi-phone text-warning me-2"></i>Aplicativo móvel complementar</li>
                            <li class="mb-2"><i class="bi bi-graph-up text-purple me-2"></i>Dashboard com análises avançadas</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Ações -->
    <div class="text-center mt-5">
        <a href="{{ asset('docs/doc_uema.pdf') }}" target="_blank" class="btn btn-danger btn-lg me-3">
            <i class="bi bi-file-earmark-pdf me-2"></i>Baixar PDF Completo
        </a>
        <a href="https://github.com/nmaramaldo/eventos-uema" target="_blank" class="btn btn-dark btn-lg">
            <i class="bi bi-github me-2"></i>Ver no GitHub
        </a>
    </div>

    <!-- Voltar ao Topo -->
    <div class="text-center mt-4">
        <a href="#" class="btn btn-outline-primary" id="backToTop">
            <i class="bi bi-arrow-up me-2"></i>Voltar ao Topo
        </a>
    </div>
</div>

@push('scripts')
<script>
    // Botão voltar ao topo
    document.getElementById('backToTop').addEventListener('click', function(e) {
        e.preventDefault();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // Destacar link ativo no índice
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.list-unstyled a');

    window.addEventListener('scroll', () => {
        let current = '';
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.clientHeight;
            if (scrollY >= (sectionTop - 100)) {
                current = section.getAttribute('id');
            }
        });

        navLinks.forEach(link => {
            link.classList.remove('active');
            link.classList.remove('fw-bold');
            if (link.getAttribute('href') === `#${current}`) {
                link.classList.add('active');
                link.classList.add('fw-bold');
            }
        });
    });

    // Suavizar scroll para âncoras
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 80,
                    behavior: 'smooth'
                });
            }
        });
    });
</script>
@endpush

@push('styles')
<style>
    .list-unstyled a.active {
        color: #0d6efd !important;
        font-weight: bold !important;
    }
    
    section {
        scroll-margin-top: 80px;
    }
    
    .card {
        transition: transform 0.2s;
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
    }
    
    .bg-purple {
        background-color: #6f42c1 !important;
    }
    
    .bg-orange {
        background-color: #fd7e14 !important;
    }
    
    .accordion-button:not(.collapsed) {
        background-color: rgba(13, 110, 253, 0.1);
        color: #0d6efd;
    }
    
    .list-group-item {
        border-left: 0;
        border-right: 0;
    }
    
    .list-group-item:first-child {
        border-top: 0;
    }
    
    .list-group-item:last-child {
        border-bottom: 0;
    }
    
    /* Estilo para badges coloridos */
    .badge.bg-primary { background-color: #0d6efd !important; }
    .badge.bg-success { background-color: #198754 !important; }
    .badge.bg-info { background-color: #0dcaf0 !important; }
    .badge.bg-warning { background-color: #ffc107 !important; color: #000; }
    .badge.bg-danger { background-color: #dc3545 !important; }
    
    /* Estilo para seções */
    section {
        padding-top: 20px;
        margin-top: -20px;
    }
</style>
@endpush
@endsection