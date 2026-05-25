<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>@yield('title', 'Organizer')</title>
        <link rel="icon" type="image/svg+xml" href="{{ asset('assets/fontawesome/dolly-solid-multicolor.svg') }}" />
        <link rel="apple-touch-icon" type="image/png" href="{{ asset('assets/fontawesome/dolly-solid-multicolor.png') }}" />
        <link rel="stylesheet"
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
            integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
            crossorigin="anonymous">
        <link rel="stylesheet" href="{{ asset('assets/organizer.css') }}">
    </head>
    <body>
        <nav class="navbar navbar-expand-lg bg-body-tertiary mb-3 sticky-top" data-bs-theme="dark">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ route('home') }}">Organizer</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        @auth
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Boxes
                                </a>
                                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('box.index') }}">See All</a>
                                    <a class="dropdown-item" href="{{ route('box.recent') }}">See Recently Changed</a>
                                    <a class="dropdown-item" href="{{ route('box.create') }}">Add New</a>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Box Models
                                </a>
                                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('box-model.index') }}">See All</a>
                                    <a class="dropdown-item" href="{{ route('box-model.create') }}">Add New</a>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Locations
                                </a>
                                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('location.index') }}">See All</a>
                                    <a class="dropdown-item" href="{{ route('location.create') }}">Add New</a>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Bulk
                                </a>
                                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('bulk.export') }}">Export Data</a>
                                </div>
                            </li>
                        @endauth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('about') }}">About</a>
                        </li>
                        @auth
                            <li class="nav-item d-lg-none">
                                <a class="nav-link" href="{{ route('profile.edit') }}">
                                    Change Your Password
                                </a>
                            </li>
                            <li class="nav-item">
                                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="nav-link btn btn-link p-0">Logout</button>
                                </form>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">Login</a>
                            </li>
                        @endauth
                    </ul>
                    @auth
                        <ul class="nav navbar-nav navbar-right me-3">
                            <li class="d-none d-lg-inline">
                                <a href="{{ route('profile.edit') }}">
                                    <img src="{{ auth()->user()->getAvatarUrl() }}" class="avatar rounded-circle img-responsive" />
                                </a>
                            </li>
                        </ul>
                        <form class="d-lg-flex d-none" role="search" action="{{ route('box.search') }}" data-bs-theme="light">
                            <input class="form-control me-2 " type="search" placeholder="Search" aria-label="Search" name="q">
                            <button class="btn btn-outline-success" type="submit">Search</button>
                        </form>
                    @endauth
                </div>
            </div>
        </nav>
        <div class="container">
            @foreach (session()->pull('success', []) as $message)
                <div class="alert alert-success">{{ $message }}</div>
            @endforeach
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    <div class="alert alert-danger">{{ $error }}</div>
                @endforeach
            @endif
        </div>
        @yield('body_full')
        @hasSection('body')
            <div class="container main-window">
                @auth
                    <div class="row justify-content-center d-lg-none d-flex">
                        <div class="col mb-3">
                            <form class="form-inline" action="{{ route('box.search') }}">
                                <div class="input-group">
                                    <input class="form-control me-sm-2" type="search" placeholder="Search" aria-label="Search" name="q">
                                    <button class="btn btn-outline-primary" type="submit"><i class="fas fa-search"></i></button>
                                    <a class="btn btn-outline-primary ms-4" href="{{ route('box.create') }}"><i class="fas fa-plus"></i></a>
                                </div>
                            </form>
                        </div>
                    </div>
                @endauth
                @yield('body')
            </div>
        @endif
        <script
            src="https://cdn.jsdelivr.net/npm/jquery@4.0.0/dist/jquery.slim.min.js"
            integrity="sha384-tcspKDb5tWvyRCOWzevlAeQgHeEzYdUHJpcgnIhcP9w4CnfD7DLAcS+k9QzLbRJO"
            crossorigin="anonymous"></script>
        <script
            src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
            integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
            crossorigin="anonymous"></script>
        <script
            src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js"
            integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y"
            crossorigin="anonymous"></script>
        <script src="https://kit.fontawesome.com/9471099de1.js" crossorigin="anonymous"></script>
        <script src="{{ asset('assets/organizer.js') }}"></script>
    </body>
</html>
