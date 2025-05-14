<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - ReuseMart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #00b14f;
            --text-dark: #212529;
            --text-muted: #6c757d;
            --bg-light: #f8f9fa;
            --border-color: #dee2e6;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-light);
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 260px;
            background-color:rgba(19, 124, 64, 0.57);
            border-right: 1px solid var(--border-color);
            padding: 24px 16px;
            transition: transform 0.3s ease;
            z-index: 1000;
        }

        .sidebar .logo {
            font-size: 1.5rem;
'adapter: font-weight: 700;
            color: var(--text-dark);
            text-align: center;
            margin-bottom: 32px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            font-weight: 500;
            border: none;
            border-left: 4px solid transparent;
            color: var(--text-dark);
            transition: all 0.3s;
            border-radius: 8px;
            text-decoration: none;
        }

        .sidebar-menu a:hover {
            background-color: var(--bg-light);
            color: var(--primary-color);
        }

        .sidebar-menu a.active {
            background-color: var(--bg-light);
            border-left: 4px solid var(--primary-color);
            color: var(--primary-color);
        }

        .sidebar-menu i {
            margin-right: 12px;
            font-size: 1.2rem;
        }

        /* Main Content */
        .main-content {
            margin-left: 260px;
            padding: 32px;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }

        /* Buttons */
        .btn-primary, .btn-success {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: 8px;
            font-weight: 500;
            padding: 10px 20px;
            transition: all 0.3s;
        }

        .btn-primary:hover, .btn-success:hover {
            background-color: #019944;
            border-color: #019944;
        }

        .btn-danger {
            border-radius: 8px;
            font-weight: 500;
            padding: 10px 20px;
        }

        /* Card */
        .card {
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 24px;
            background-color: #ffffff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .sidebar-toggle {
                display: block;
                position: fixed;
                top: 16px;
                left: 16px;
                z-index: 1100;
                background-color: var(--primary-color);
                color: white;
                border: none;
                padding: 10px;
                border-radius: 8px;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">Owner Panel</div>
        <nav class="sidebar-menu">
            <a href="{{ route('owner.dashboard') }}" class="{{ Route::is('owner.dashboard') ? 'active' : '' }}">
                <i class="bi bi-house-door"></i> Dashboard
            </a>
            <a href="{{ route('owner.donation.requests') }}" class="{{ Route::is('owner.donation.requests') ? 'active' : '' }}">
                <i class="bi bi-list-check"></i> Daftar Request Donasi
            </a>
            <a href="{{ route('owner.donation.history') }}" class="{{ Route::is('owner.donation.history') ? 'active' : '' }}">
                <i class="bi bi-clock-history"></i> History Donasi
            </a>
            <a href="{{ route('owner.allocate.items') }}" class="{{ Route::is('owner.allocate.items') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i> Alokasi Barang
            </a>
            <a href="{{ route('owner.update.donation') }}" class="{{ Route::is('owner.update.donation') ? 'active' : '' }}">
                <i class="bi bi-pencil-square"></i> Update Donasi
            </a>
            <a href="{{ route('owner.rewards') }}" class="{{ Route::is('owner.rewards') ? 'active' : '' }}">
                <i class="bi bi-gift"></i> Poin Reward
            </a>
            <form action="{{ route('logout') }}" method="POST" class="mt-4">
                @csrf
                <button type="submit" class="btn btn-danger w-100">
                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                </button>
            </form>
        </nav>
    </div>

    <!-- Main Content -->
    <button class="sidebar-toggle d-none" onclick="toggleSidebar()">
        <i class="bi bi-list"></i>
    </button>
    <div class="main-content">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.classList.toggle('active');
        }
    </script>
    @stack('scripts')
</body>
</html>