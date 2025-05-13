<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Admin Panel')</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            background-color: #f8fafc;
        }

        .topbar {
            height: 120px;
            background: #fff;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ccc;
        }

        .topbar-left img {
            height: 80px;
        }

        .main-wrapper {
            display: flex;
            height: calc(100vh - 120px); /* sisa tinggi layar setelah topbar */
        }

        .sidebar {
            width: 220px;
            background: #E2E8E6;
            padding: 1.5rem 1rem;
        }

        .main-content {
            flex: 1;
            padding: 2rem;
            overflow-y: auto;
            background-color: #F8FAF9;
        }

        .menu-item {
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #212529;
            text-decoration: none;
            font-size: 16px;
            padding: 6px 0;
            transition: background 0.2s;
        }

        .menu-item:hover {
            color: #212529;
            background-color: #4DD6A5 ;
            padding-left: 6px;
            border-radius: 4px;
        }

        .menu-item.active {
            background-color: #30B878;
            padding-left: 6px;
            border-radius: 4px;
        }
        
        .card-stat {
            width: 240px;
            height: 80px;
            background-color: #FFFFFF;
            border: 1px solid #CECECE;
            padding: 1rem;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .card-stat span:first-child {
            font-size: 16px;
            font-weight: 400;
        }

        .card-stat span:last-child {
            font-size: 20px;
        }
        
        .card-value {
            font-size: 20px;
            font-weight: 400;
        }

    </style>
</head>

<body>
    <!-- TOPBAR -->
    <div class="topbar">
        <div class="topbar-left">
            <a href="{{ route('admin.dashboard') }}">
                <img src="{{ asset('images/Reusemart kiri.png') }}" alt="ReUse Mart">
            </a>
        </div>
        <div style="display: flex; align-items: center;">
            <span style="font-size: 18px; margin-right: 40px;">🛒 Marketplace</span>
            <img src="{{ asset('images/avatar.png') }}" alt="avatar" height="40" style="border-radius: 50%; margin-right: 5px;">
            <span style="font-weight: 400;">Sinta Admin</span>
        </div>
    </div>

    <!-- MAIN CONTENT LAYOUT -->
    <div class="main-wrapper">
        <!-- SIDEBAR -->
        <div class="sidebar">
            <a href="{{ route('admin.employees.index') }}" 
                class="menu-item {{ request()->is('admin/employees*') ? 'active' : '' }}">👥 Employees</a>
            <a href="{{ route('admin.roles.index') }}" 
                class="menu-item {{ request()->is('admin/roles*') ? 'active' : '' }}">💼 Roles</a>
            <a href="{{ route('admin.penitip.index') }}" 
                class="menu-item {{ request()->is('admin/item-owners*') ? 'active' : '' }}">📦 Item Owners</a>
            <a href="{{ route('admin.pembeli.index') }}" 
                class="menu-item {{ request()->is('admin/customers*') ? 'active' : '' }}">🛍️ Customers</a>
            <a href="{{ route('admin.organisasi.index') }}" 
                class="menu-item {{ request()->is('admin/organizations*') ? 'active' : '' }}">🏢 Organizations</a>
            <a href="{{ route('admin.produk.index') }}" 
                class="menu-item {{ request()->is('admin/products*') ? 'active' : '' }}">🏷️ Products</a>
            <a href="{{ route('admin.merch.index') }}" 
                class="menu-item {{ request()->is('admin/merchandise*') ? 'active' : '' }}">🎁 Merchandise</a>
        </div>

        <!-- DYNAMIC CONTENT -->
        <div class="main-content">
            @yield('content')
        </div>
    </div>
</body>
</html>
