@extends('Admin.admin')

@section('title', 'Dashboard Admin')

@section('content')
    @php $pegawai = Auth::guard('pegawai')->user(); @endphp

    @if($pegawai)
        <h2>Welcome back, {{ $pegawai->nama_pegawai }}!</h2>
    @else
        <script>window.location.href = "{{ route('login') }}";</script>
    @endif


    <div style="display: flex; gap: 1rem; margin-top: 1rem;">
        <div class="card-stat">
            <span>Employees</span>
            <span class="card-value">👥 {{ $jumlahPegawai }}</span>
        </div>
        <div class="card-stat">
            <span>Item Owners</span>
            <span class="card-value">📦 {{ $jumlahPenitip }}</span>
        </div>
        <div class="card-stat">
            <span>Organizations</span>
            <span class="card-value">🏢 {{ $jumlahOrganisasi }}</span>
        </div>
        <div class="card-stat">
            <span>Customers</span>
            <span class="card-value">🛍️ {{ $jumlahPembeli }}</span>
        </div>
    </div>
@endsection
