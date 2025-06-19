<div class="card shadow-sm">
    <div class="list-group list-group-flush">
        <div class="small fw-bold text-uppercase px-3 pt-2 text-secondary">My Account</div>
        <a href="{{ route('pembeli.profile') }}"
            class="list-group-item list-group-item-action {{ request()->routeIs('pembeli.profile') ? 'active' : '' }}">
            Profile
        </a>
        <a href="{{ route('pembeli.alamat') }}"
            class="list-group-item list-group-item-action {{ request()->routeIs('pembeli.alamat') ? 'active' : '' }}">
            Address
        </a>
        <a href="{{ route('password.reset') }}"
            class="list-group-item list-group-item-action {{ request()->routeIs('password.reset') ? 'active' : '' }}">
            Change Password
        </a>

        <div class="small fw-bold text-uppercase px-3 pt-3 text-secondary">My Purchase</div>
        <a href="{{ route('pembeli.purchase') }}"
            class="list-group-item list-group-item-action {{ request()->routeIs('pembeli.purchase') ? 'active' : '' }}">
            Purchase History
        </a>

        <div class="small fw-bold text-uppercase px-3 pt-3 text-secondary">ReuseMart Coins</div>
        <a href="{{ route('pembeli.reward') }}"
            class="list-group-item list-group-item-action {{ request()->routeIs('pembeli.reward') ? 'active' : '' }}">
            Reward Coins
        </a>
    </div>
</div>
