<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm px-4 py-3">

    <div class="container-fluid">
        <div class="d-flex align-items-center">
            <a class="navbar-brand d-flex align-items-center me-4" href="{{ route('welcome') }}">
                <img src="/assets/images/logoNoBg.png" width="60" height="60" class="me-2" alt="Logo">
                <strong>ReUseMart</strong>
            </a>
        </div>

        <form class="d-flex flex-grow-1 mx-4">
            <input class="form-control me-2" type="search" placeholder="What are you looking for?" aria-label="Search">
            <button class="btn btn-outline-success" type="submit">Search</button>
        </form>

        <div class="d-flex align-items-center">
            @php
                $role = session('role');
                $user = session('user');
            @endphp

            @if ($role && $user)
                <div class="d-flex align-items-center me-3">
                    <a href="{{ route('welcome') }}" class="me-3 text-decoration-none text-dark">Home</a>
                    <a href="{{ route('about') }}" class="me-3 text-decoration-none text-dark">About</a>
                    <a href="{{ route('pembeli.cart') }}" class="text-success position-relative">
                        <i class="fa-solid fa-cart-shopping fs-4"></i>
                    </a>
                </div>

                <div class="dropdown">
                    <button class="btn dropdown-toggle d-flex align-items-center" type="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center me-2"
                            style="width: 32px; height: 32px;">
                            <i class="fa-solid fa-user fs-6"></i>
                        </div>
                        {{ $user['nama'] ?? 'User' }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        @if ($role === 'pembeli')
                            <li><a class="dropdown-item" href="{{ route('pembeli.profile') }}">My Account</a></li>
                            {{-- <li><a class="dropdown-item" href="{{ route('pembeli.purchase') }}">My Order</a></li> --}}
                        @elseif ($role === 'penitip')
                            <li><a class="dropdown-item" href="{{ route('penitip.profile') }}">My Account</a></li>
                            <li><a class="dropdown-item" href="{{ route('penitip.myproduct') }}">My Product</a></li>
                        @elseif ($role === 'organisasi')
                            <li><a class="dropdown-item" href="{{ route('organisasi.profile') }}">My Account</a></li>
                            <li><a class="dropdown-item" href="{{ route('organisasi.request') }}">Request Donasi
                                    Saya</a></li>
                        @endif
                        <li>
                            <form id="logout-form" action="{{ route('logout.submit') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item">Log Out</button>
                            </form>

                        </li>
                    </ul>
                </div>
            @else
                <div class="d-flex align-items-center">
                    <a href="#" class="btn btn-success me-2" data-bs-toggle="modal"
                        data-bs-target="#registerModal">Register</a>
                    <a href="#" class="btn btn-success" data-bs-toggle="modal"
                        data-bs-target="#loginModal">Login</a>
                </div>
            @endif

        </div>
    </div>
</nav>