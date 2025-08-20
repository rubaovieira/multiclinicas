<style>
    .nav-link {
        color: #000000;
    }

    .nav-link:hover {
        color: #0258ed;
    }


    .content {
        background-color: #f5f5f5;
    }

    .breadcrumb {
        background-color: white;
    }
</style>

<div class="d-flex flex-column flex-md-row">

    <!-- Menu Lateral -->
    <nav class="flex-shrink-0 d-none d-md-block" style="height: 100vh;">
        <div style="display:flex">
            <div id="desktopMenu" class="p-2" style="height: 100vh; border-right: 2px solid #56A4EE;">
                <div class="d-flex justify-content-center align-items-center p-2" style="border-bottom:1px solid silver;">
                    <h5 class="text-center mb-0">
                        <a href="{{ route('home') }}" class="text-dark text-decoration-none d-flex align-items-center">
                            <img style="border-radius: 50%; border: 2px solid #56A4EE;"
                                src="{{ config('app.logo_url') }}" alt="Security Icon" width="30" class="me-2">
                            <span style="font-weight: bold; color: #333;" id="username">
                                @auth
                                    {{ strtolower(auth()->user()->name) }}
                                @endauth
                            </span>
                        </a>
                    </h5>
                </div>
                <ul class="nav flex-column" id="navItems">

                    @if (Auth::user()->perfil === 'master')
                        <li class="nav-item">
                            <a href="{{ route('clinics') }}"
                                class="nav-link {{ request()->routeIs('clinics*') ? 'active' : '' }}">
                                <i class="bi bi-hospital"></i>
                                <span class="nav-text">Clínicas</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin-users') }}"
                                class="nav-link {{ request()->routeIs('admin-users*') ? 'active' : '' }}">
                                <i class="bi bi-people"></i>
                                <span class="nav-text">Administradores</span>
                            </a>
                        </li>
                    @elseif(auth()->user()->perfil === 'cliente')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('client.appointments*') ? 'active' : '' }}"
                                href="{{ route('client.appointments') }}">
                                <i class="bi bi-calendar-check"></i>
                                <span class="nav-text">Minhas Consultas</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}"
                                href="{{ route('home') }}">
                                <i class="bi bi-house-door"></i>
                                <span class="nav-text">Início</span>
                            </a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link active" href="{{ route('home') }}">
                                <i class="bi bi-house-door"></i>
                                <span class="nav-text">Inicio</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="{{ route('clients') }}">
                                <i class="bi bi-people"></i>
                                <span class="nav-text">Pacientes</span>
                            </a>
                        </li>

                        @if (auth()->user()->perfil === 'medico')
                            <li class="nav-item">
                                <a class="nav-link active" href="{{ route('appointments') }}">
                                    <i class="bi bi-calendar-check"></i>
                                    <span class="nav-text">Consultas</span>
                                </a>
                            </li>
                        @endif

                        @if (auth()->user()->perfil === 'admin')
                            <li class="nav-item">
                                <a class="nav-link active" href="{{ route('admin.appointments') }}">
                                    <i class="bi bi-calendar-check"></i>
                                    <span class="nav-text">Agendamentos</span>
                                </a>
                            </li>
                        @endif

                        <li class="nav-item">
                            <a class="nav-link active" href="{{ route('services') }}">
                                <i class="bi bi-list-ul"></i>
                                <span class="nav-text">Atendimentos</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="{{ route('procedures') }}">
                                <i class="bi bi-receipt-cutoff"></i>
                                <span class="nav-text">Procedimentos</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="{{ route('health_plans') }}">
                                <i class="bi bi-credit-card-2-front"></i>
                                <span class="nav-text">Convênios</span>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="estoqueDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-box-seam"></i>
                                <span class="nav-text">Estoque</span>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="estoqueDropdown">
                                <li><a class="dropdown-item" href="{{ route('controle_estoque') }}">Controle de
                                        Estoque</a></li>
                                <li><a class="dropdown-item" href="{{ route('saidas') }}">Saídas</a></li>
                                <li><a class="dropdown-item" href="{{ route('entradas') }}">Entradas</a></li>
                                <li><a class="dropdown-item" href="{{ route('produtos') }}">Produtos</a></li>
                            </ul>
                        </li>
                        @if (auth()->user()->perfil === 'admin')
                            <li class="nav-item">
                                <a class="nav-link active" href="{{ route('users') }}">
                                    <i class="bi bi-person"></i>
                                    <span class="nav-text">Equipe</span>
                                </a>
                            </li>
                        @endif
                    @endif

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('logout') }}">
                            <i class="bi bi-box-arrow-left"></i>
                            <span class="nav-text">Sair</span>
                        </a>
                    </li>

                </ul>
            </div>
            <div style="margin-left:-14px; margin-top: 42px; ">
                <div style="background-color: #0A64B8; border-radius: 50%; width: 25px; height: 25px; display: flex; justify-content: center; align-items: center; cursor:pointer;"
                    onclick="toggleNav()">
                    <i id="toggleIcon" class="bi bi-caret-left-fill" style="color: white; font-size:10px;"></i>
                </div>
            </div>
        </div>
    </nav>


    <!-- Botão de Hamburguer -->
    <div class="d-md-none p-3 d-flex justify-content-between align-items-center">
        <div>
            <img src="{{ config('app.logo_url') }}" alt="Security Icon" width="100" class="me-2">
        </div>
        <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu"
            aria-controls="mobileMenu">
            <i class="bi bi-list"></i>
        </button>
    </div>


    <!-- Menu Offcanvas para dispositivos móveis -->
    <div class="offcanvas offcanvas-start" id="mobileMenu" tabindex="-1" aria-labelledby="mobileMenuLabel">

        <div class="offcanvas-header">

            <h5 class="text-center">
                <a href="{{ route('home') }}" class="text-dark text-decoration-none">
                    <img src="{{ config('app.logo_url') }}" alt="Security Icon" width="60" class="me-2">
                </a>
            </h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link active" href="{{ route('home') }}">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('logout') }}">Sair</a>
                </li>
            </ul>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ...
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (localStorage.getItem('navCollapsed') == 'true') {
                $(".nav-text").hide();
                const navItems = document.getElementById('navItems');
                const toggleIcon = document.getElementById('toggleIcon');
                const username = document.getElementById('username');

                username.style.display = 'none'; // Oculta o nome do usuário
                toggleIcon.classList.remove('bi-caret-left-fill'); // Troca o ícone
                toggleIcon.classList.add('bi-caret-right-fill');
                toggleIcon.style.fontSize = '12px'; // Ajusta o tamanho do ícone
            }
        });
        let isCollapsed = false;

        function toggleNav() {
            const navItems = document.getElementById('navItems');
            const toggleIcon = document.getElementById('toggleIcon');
            const username = document.getElementById('username');

            if (!isCollapsed) {
                localStorage.setItem('navCollapsed', true);
                $(".nav-text").hide();
                username.style.display = 'none'; // Oculta o nome do usuário
                toggleIcon.classList.remove('bi-caret-left-fill'); // Troca o ícone
                toggleIcon.classList.add('bi-caret-right-fill');
                toggleIcon.style.fontSize = '12px'; // Ajusta o tamanho do ícone
            } else {
                localStorage.setItem('navCollapsed', false);
                $(".nav-text").show();
                username.style.display = 'block'; // Exibe o nome do usuário
                toggleIcon.classList.remove('bi-caret-right-fill'); // Troca o ícone
                toggleIcon.classList.add('bi-caret-left-fill');
                toggleIcon.style.fontSize = '10px'; // Ajusta o tamanho do ícone
            }

            isCollapsed = !isCollapsed; // Alterna o estado
        }
    </script>

    <div class="content p-4 flex-grow-1">

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                @php
                    $breadcrumbs = \App\Helpers\BreadcrumbHelper::generate();
                @endphp

                @foreach ($breadcrumbs as $breadcrumb)
                    @if ($loop->last)
                        <li class="breadcrumb-item active" aria-current="page">{{ $breadcrumb['title'] }}</li>
                    @else
                        <li class="breadcrumb-item">
                            <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['title'] }}</a>
                        </li>
                    @endif
                @endforeach
            </ol>
        </nav>


        @yield('content')
    </div>
</div>
