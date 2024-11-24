<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title></title>
    <!-- Link ke CSS Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Link ke Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    
    <style>
        /* Custom style untuk sidebar */
        .sidebar {
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            background-color: #343a40;
            padding-top: 20px;
            color: white;
        }
        .sidebar .nav-link {
            color: #bbb;
        }
        .sidebar .nav-link:hover {
            color: white;
            background-color: #495057;
        }
        .content {
            margin-left: 250px;
            padding: 20px;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            .content {
                margin-left: 0;
            }
        }
    </style>
    @yield('css')
</head>
<body>

    <!-- Navbar untuk User Info -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            {{-- <a class="navbar-brand" href="#">SMK PUSDIKHUBAD</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button> --}}
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ Auth::user()->guru->nama }}
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>
                            <form id="logout-form" action="{{route('logout')}}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="image-container">
            <img src="https://ebc-didactic.com/wp-content/uploads/2023/01/smk-pusdikhub-cimahi.webp" style="width: 80px;" alt="Logo" class="center-image">
            <a class="navbar-brand mt-3" href="#">SMK PUSDIKHUBAD</a>
        </div>
        <hr>
        <ul class="nav flex-column">
            @if (Auth::user()->role->nama === "admin")
            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'kelas.index' ? 'bg-primary':''}}" href={{route('kelas.index')}}><i class="fas fa-school"></i> Kelas</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'mapel.index' ? 'bg-primary':''}}" href={{route('mapel.index')}}><i class="fas fa-book"></i> Mata Pelajaran</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'guru.index' ? 'bg-primary':''}}" href="{{route('guru.index')}}"><i class="fas fa-chalkboard-teacher"></i> Guru</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'siswa.index' ? 'bg-primary':''}}" href="{{route('siswa.index')}}"><i class="fas fa-user-graduate"></i> Siswa</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'role.index' ? 'bg-primary':''}}" href="{{route('role.index')}}"><i class="fas fa-user-shield"></i> Role</a>
            </li>
            @endif
            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'absensi.index' ? 'bg-primary':''}}" href="{{route('absensi.index')}}"><i class="fas fa-clipboard-check"></i> Absensi</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'agenda.index' ? 'bg-primary':''}}" href="{{route('agenda.index')}}"><i class="fas fa-calendar-alt"></i> Agenda</a>
            </li>
        </ul>
    </div>

    <!-- Content Area -->
    <div class="content">
        @yield('content')  <!-- This will yield content from specific views -->
    </div>
    <!-- end Content Area -->

    <!-- Link ke JS Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    
    @yield('script')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.3/js/dataTables.bootstrap5.min.js"></script>
</body>
</html>
