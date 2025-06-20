<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'CS Panel')</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


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
            position: relative;
        }

        .topbar-left img {
            height: 80px;
        }

        .main-wrapper {
            display: flex;
            height: calc(100vh - 120px);
        }

        .sidebar {
            width: 230px;
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
            font-size: 15px;
            padding: 6px 0;
            transition: background 0.2s;
        }

        .menu-item:hover {
            color: #212529;
            background-color: #4DD6A5;
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
            font-size: 12px;
            font-weight: 400;
        }

        .card-stat span:last-child {
            font-size: 20px;
        }

        .card-value {
            font-size: 20px;
            font-weight: 400;
        }

        /* Profile dropdown */
        .profile-dropdown {
            position: relative;
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            top: 50px;
            right: 2rem;
            background-color: white;
            min-width: 120px;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 999;
            border-radius: 6px;
            overflow: hidden;
        }

        .dropdown-content form {
            margin: 0;
        }

        .dropdown-content button {
            width: 100%;
            padding: 10px;
            background: none;
            border: none;
            text-align: left;
            font-family: 'Poppins', sans-serif;
            font-weight: 400;
            cursor: pointer;
            color: #212529;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 4px;
            transition: background-color 0.2s ease-in-out, transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .dropdown-content button:hover {
            background-color: #f1f1f1;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>

<body>
    <div class="topbar">
        <div class="topbar-left">
            <a href="{{ route('cs.dashboard') }}">
                <img src="{{ asset('images/Reusemart kiri.png') }}" alt="ReUse Mart">
            </a>
        </div>
        <div class="profile-dropdown" onclick="toggleDropdown()">
            <span style="font-size: 18px; margin-right: 40px;">🛒 Marketplace</span>
            <img src="{{ asset('images/avatar.png') }}" alt="avatar" height="40"
                style="border-radius: 50%; margin-right: 5px;">
            <span style="font-weight: 400;">
                {{ Auth::guard('pegawai')->user()->nama_pegawai }} ⏷
            </span>

            <div class="dropdown-content" id="dropdownContent">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">🔓 Logout</button>
                </form>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT LAYOUT -->
    <div class="main-wrapper">
        <!-- SIDEBAR -->
        <div class="sidebar">
            <a href="{{ route('cs.penitip.index') }}"
                class="menu-item {{ request()->is('cs/item-owners*') ? 'active' : '' }}">📦 Item Owners</a>
            <a href="{{ route('cs.merchandise-claim.index') }}"
                class="menu-item {{ request()->is('cs/merchandise-claims*') ? 'active' : '' }}">🎁 Merchandise Claims</a>
            <a href="{{ route('transaksi-pembelian.index') }}"
                class="menu-item {{ request()->is('cs/transaksi-pembelian*') ? 'active' : '' }}">💳 Konfirmasi
                Transaksi</a>
        </div>

        <!-- DYNAMIC CONTENT -->
        <div class="main-content">
            @yield('content')
        </div>
    </div>

    <!-- TOGGLE SCRIPT -->
    <script>
        function toggleDropdown() {
            const dropdown = document.getElementById("dropdownContent");
            dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
        }

        window.onclick = function(e) {
            if (!e.target.closest('.profile-dropdown')) {
                document.getElementById("dropdownContent").style.display = "none";
            }
        }
    </script>
</body>

</html>