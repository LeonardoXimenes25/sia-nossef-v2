<header id="header" class="header fixed-top shadow-lg">
    <div class="branding d-flex align-items-center">
        <div class="container position-relative d-flex align-items-center justify-content-between">

            <!-- Logo kiri -->
            <a href="{{ route('home') }}" class="logo d-flex align-items-center">
                <h1 class="sitename mb-0">ESG. NOSSEF</h1>
            </a>

            <!-- Menu utama di tengah -->
            <nav id="navmenu" class="navmenu d-none d-xl-flex flex-grow-1 justify-content-center">
                <ul class="d-flex list-unstyled mb-0">
                    <li><a href="{{ route('home') }}" class="{{ Route::is('home') ? 'active' : '' }}">Baranda</a></li>
                    <li><a href="{{ route('about') }}" class="{{ Route::is('about') ? 'active' : '' }}">Konaba Ami</a></li>
                    <li><a href="{{ route('schedule') }}">Horariu</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle " href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Dadus Eskola
                        </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{route('students.index')}}">Lista Alunos</a></li>
                        <li><a class="dropdown-item" href="{{ route('teachers.index') }}">Lista Professores</a></li>
                    </ul>
                    </li>
                    <li><a href="{{route('news.index')}}">Portal Informasaun</a></li>
                </ul>
            </nav>

            <!-- Login di kanan -->
            <a href="/admin/login" class="btn text-white" style="background-color: #0099ff; border-color: #0099ff;">
                Login
            </a>


            <!-- Mobile toggle -->
            <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>

        </div>
    </div>
</header>
