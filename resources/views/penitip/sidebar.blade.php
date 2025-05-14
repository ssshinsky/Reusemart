<div class="list-group shadow-sm sidebar-menu">
    <a href="{{ route('penitip.profile') }}"
        class="list-group-item {{ request()->routeIs('penitip.profile') ? 'active' : '' }}">
        <i class="bi bi-person"></i> My Account
    </a>
    <a href="{{ route('penitip.profile') }}"
        class="list-group-item {{ request()->routeIs('penitip.profile') ? 'active' : '' }}">
        <i class="bi bi-key"></i> Change Password
    </a>
    <a href="{{ route('penitip.myproduct') }}"
        class="list-group-item {{ request()->routeIs('penitip.myproduct') ? 'active' : '' }}">
        <i class="bi bi-box-seam"></i> My Product
    </a>
    <a href="{{ route('penitip.transaction') }}"
        class="list-group-item {{ request()->routeIs('penitip.transaction') ? 'active' : '' }}">
        <i class="bi bi-receipt"></i> My Transaction
    </a>
    <a href="{{ route('penitip.rewards') }}"
        class="list-group-item {{ request()->routeIs('penitip.rewards') ? 'active' : '' }}">
        <i class="bi bi-coin"></i> Balances and Rewards
    </a>
</div>
