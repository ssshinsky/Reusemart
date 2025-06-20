<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - ReuseMart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @stack('styles')
    <style>
        :root {
            --primary-color: #00b14f; /* Hijau utama dari desain awal */
            --secondary-color: #019944; /* Variasi hijau lebih gelap */
            --text-dark: #1a2c34;
            --text-muted: #6c757d;
            --bg-light: #f4f6f9;
            --border-color: #e0e4e8;
            --sidebar-bg: rgba(19, 124, 64, 0.57); /* Sesuai desain awal */
            --card-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            overflow-x: hidden;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 260px;
            background-color: var(--sidebar-bg); /* Tetap mirip desain awal */
            border-right: 1px solid var(--border-color);
            padding: 30px 20px;
            transition: transform 0.3s ease, width 0.3s ease;
            z-index: 1000;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar .logo {
            font-size: 1.8rem;
            font-weight: 600;
            color: #ffffff;
            text-align: center;
            margin-bottom: 40px;
            letter-spacing: 1px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 14px 20px;
            font-weight: 500;
            color: #ffffff;
            border-left: 4px solid transparent;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
            margin-bottom: 8px;
        }

        .sidebar-menu a:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }

        .sidebar-menu a.active {
            background-color: rgba(255, 255, 255, 0.2);
            border-left: 4px solid var(--primary-color);
            color: #ffffff;
            font-weight: 600;
        }

        .sidebar-menu i {
            margin-right: 14px;
            font-size: 1.3rem;
        }

        /* Main Content */
        .main-content {
            margin-left: 260px;
            padding: 40px;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
            background-color: var(--bg-light);
        }

        /* Header di Main Content */
        .main-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .main-header h2 {
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--text-dark);
        }

        .main-header .breadcrumb {
            background: none;
            padding: 0;
            margin-bottom: 0;
        }

        .breadcrumb-item a {
            color: var(--text-muted);
            text-decoration: none;
        }

        .breadcrumb-item.active {
            color: var(--primary-color);
        }

        /* Buttons */
        .btn-primary, .btn-success {
            background-color: var(--primary-color); /* Kembali ke warna utama */
            border: none;
            border-radius: 10px;
            font-weight: 500;
            padding: 10px 24px;
            transition: all 0.3s ease;
            color: #ffffff;
        }

        .btn-primary:hover, .btn-success:hover {
            background-color: var(--secondary-color); /* Variasi gelap */
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 177, 79, 0.3);
        }

        .btn-danger {
            background-color: #dc3545;
            border: none;
            border-radius: 10px;
            font-weight: 500;
            padding: 10px 24px;
            transition: all 0.3s ease;
            color: #ffffff;
        }

        .btn-danger:hover {
            background-color: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(220, 53, 69, 0.3);
        }

        .btn-outline-primary {
            border-color: var(--primary-color);
            color: var(--primary-color);
            font-weight: 500;
            padding: 10px 20px;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 177, 79, 0.3);
        }

        /* Card */
        .card {
            border: none;
            border-radius: 16px;
            padding: 24px;
            background-color: #ffffff;
            box-shadow: var(--card-shadow);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }

        /* Text */
        .text-success {
            color: var(--primary-color) !important;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: 200px;
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                padding: 20px;
            }

            .sidebar-toggle {
                display: block;
                position: fixed;
                top: 20px;
                left: 20px;
                z-index: 1100;
                background-color: var(--primary-color);
                color: white;
                border: none;
                padding: 10px 14px;
                border-radius: 10px;
                transition: all 0.3s ease;
            }

            .sidebar-toggle:hover {
                transform: scale(1.1);
            }

            .main-header h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">Gudang Panel</div>
        <nav class="sidebar-menu">
            <a href="{{ route('gudang.dashboard') }}" class="{{ Route::is('gudang.dashboard') ? 'active' : '' }}">
                <i class="bi bi-house-door"></i> Dashboard
            </a>
            <a href="{{ route('gudang.add.transaction') }}" class="{{ Route::is('gudang.add.transaction') ? 'active' : '' }}">
                <i class="bi bi-plus-circle"></i> Tambah Transaksi
            </a>
            <a href="{{ route('gudang.transaction.list') }}" class="{{ Route::is('gudang.transaction.list') ? 'active' : '' }}">
                <i class="bi bi-list-check"></i> Daftar Transaksi
            </a>
            <a href="{{ route('gudang.item.list') }}" class="{{ Route::is('gudang.item.list') ? 'active' : '' }}">
                <i class="bi bi-boxes"></i> Daftar Barang
            </a>
            <a href="{{ route('gudang.transaksi.pengiriman') }}" class="{{ request()->routeIs('gudang.transaksi.pengiriman') ? 'active' : '' }}">
                <i class="bi bi-truck me-1"></i> Pengiriman & Pengambilan
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.classList.toggle('active');
        }
    </script>
    @stack('scripts')
</body>
</html>