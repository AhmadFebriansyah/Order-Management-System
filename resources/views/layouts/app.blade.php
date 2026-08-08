<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Order Management System')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ route('products.index') }}">OMS</a>

            @guest
                <div class="navbar-nav ms-auto ml-auto">
                    <a class="nav-link" href="{{ route('login') }}">Login</a>
                    <a class="nav-link" href="{{ route('register') }}">Register</a>
                </div>
            @else
                <div class="collapse navbar-collapse w-100" id="navbarNav">
                    
                    <div class="navbar-nav mx-auto">
                        <a class="nav-link" href="{{ route('products.index') }}">Produk</a>
                        <a class="nav-link" href="{{ route('cart.index') }}">
                            Keranjang
                            @if (count(session('cart', [])) > 0)
                                <span class="badge bg-danger">{{ count(session('cart', [])) }}</span>
                            @endif
                        </a>
                        <a class="nav-link" href="{{ route('orders.index') }}">Riwayat Order</a>
                    </div>

                    <div class="navbar-nav">
                        <span class="nav-link text-white">Halo, {{ auth()->user()->name }}</span>
                        <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                    </div>

                </div>
            @endguest
        </div>
    </nav>

    <div class="container pb-5">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>
</body>
</html>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');
        const eyeIcon = document.querySelector('#eyeIcon');

        togglePassword.addEventListener('click', function () {
            const isPassword = password.getAttribute('type') === 'password';
            
            password.setAttribute('type', isPassword ? 'text' : 'password');
            
            // Ubah ikon: password (hide), password terbuka (eye)
            if (isPassword) {
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            } else {
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            }
        });
    });
</script>