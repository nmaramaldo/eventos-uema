<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
  <div class="container">

    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#main-navbar">
        <span class="icon icon-bar"></span>
        <span class="icon icon-bar"></span>
        <span class="icon icon-bar"></span>
      </button>

      <a href="{{ route('front.home') }}" class="navbar-brand" style="display:flex;align-items:center;gap:10px;">
        <img src="{{ asset('new-event/images/uema-logo.png') }}" alt="UEMA" style="height:28px">
        <span>UEMA Eventos</span>
      </a>
    </div>

    <div class="collapse navbar-collapse" id="main-navbar">
      <ul class="nav navbar-nav navbar-right">
        <li><a href="{{ route('front.home') }}">Início</a></li>
        <li><a href="{{ route('front.eventos.index') }}">Eventos</a></li>
        <li><a href="#palestrantes">Palestrantes</a></li>
        <li><a href="#programacao">Programação</a></li>
        <li><a href="#inscricao">Inscrição</a></li>

        @auth
          @can('manage-users')
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                Cadastro <span class="caret"></span>
              </a>
              <ul class="dropdown-menu">
                <li><a href="{{ route('admin.usuarios.index') }}">Usuários</a></li>
                <li><a href="{{ route('eventos.index') }}">Eventos</a></li>
                <li><a href="{{ route('palestrantes.index') }}">Palestrantes</a></li>
                <li><a href="{{ route('inscricoes.index') }}">Inscrições</a></li>
              </ul>
            </li>
          @endcan

          <li><a href="{{ route('dashboard') }}">Painel</a></li>
          <li>
            <form method="POST" action="{{ route('logout') }}" style="display:inline">
              @csrf
              <button type="submit" class="btn btn-link navbar-btn" style="padding:15px 15px;">Sair</button>
            </form>
          </li>
        @else
          <li>
            <a class="btn btn-primary btn-login" href="{{ route('login') }}" style="color:#fff; margin-left:10px;">
              Entrar
            </a>
          </li>
        @endauth
      </ul>
    </div>

  </div>
</nav>
