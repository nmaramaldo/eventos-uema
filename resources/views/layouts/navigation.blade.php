<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
  <div class="container">

    {{-- Marca (logo + título) --}}
    <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('front.home') }}">
      <img src="{{ url('new-event/images/uema-logo.png') }}" alt="UEMA" height="28">
      <span class="fw-semibold">UEMA Eventos</span>
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
            aria-controls="mainNavbar" aria-expanded="false" aria-label="Alternar navegação">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="mainNavbar">
      {{-- Links à esquerda --}}
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        {{-- Início (home pública) --}}
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('front.home') ? 'active' : '' }}"
             href="{{ route('front.home') }}">Início</a>
        </li>

        {{-- Lista pública de eventos --}}
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('front.eventos.*') ? 'active' : '' }}"
             href="{{ route('front.eventos.index') }}">Eventos</a>
        </li>

        {{-- Área interna (somente autenticado) --}}
        @auth
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
               href="{{ route('dashboard') }}">Painel de Controle</a>
          </li>

          @can('viewAny', App\Models\Event::class)
            <li class="nav-item">
              <a class="nav-link {{ request()->routeIs('eventos.*') ? 'active' : '' }}"
                 href="{{ route('eventos.index') }}">Gerenciar Eventos</a>
            </li>
          @endcan

          @can('viewAny', App\Models\User::class)
            <li class="nav-item">
              <a class="nav-link {{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}"
                 href="{{ route('admin.usuarios.index') }}">Administração</a>
            </li>
          @endcan
        @endauth
      </ul>

      {{-- Usuário (lado direito) --}}
      <ul class="navbar-nav ms-auto">
        @auth
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center gap-1" href="#" id="userMenu"
               role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="bi bi-person-circle"></i> {{ auth()->user()?->name ?? 'Conta' }}
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userMenu">
              <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Perfil</a></li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <form method="POST" action="{{ route('logout') }}">
                  @csrf
                  <button class="dropdown-item" type="submit">Sair</button>
                </form>
              </li>
            </ul>
          </li>
        @endauth

        @guest
          <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Entrar</a></li>
          @if (Route::has('register'))
            <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Cadastrar</a></li>
          @endif
        @endguest
      </ul>
    </div>
  </div>
</nav>
