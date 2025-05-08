@extends('Admin.admin')

@section('title', 'Dashboard Admin')

@section('content')
    <h2>Welcome back, Sinta Admin!</h2>

    <div style="display: flex; gap: 1rem; margin-top: 1rem;">
        <div class="card-stat">
            <span>Employees</span>
            <span class="card-value">👥 100</span>
        </div>
        <div class="card-stat">
            <span>Item Owners</span>
            <span class="card-value">📦 1.000</span>
        </div>
        <div class="card-stat">
            <span>Organizations</span>
            <span class="card-value">🏢 31</span>
        </div>
        <div class="card-stat">
            <span>Customers</span>
            <span class="card-value">🛍️ 10.000</span>
        </div>
    </div>
@endsection
