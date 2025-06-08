@extends('owner.owner_layout')

@section('title', 'Laporan Penjualan Bulanan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">Laporan Penjualan Bulanan</h2>
            <p class="text-muted">ReUse Mart, Jl. Green Eco Park No. 456 Yogyakarta</p>
        </div>
        <form method="GET" action="{{ route('owner.monthly.sales.report') }}" class="d-flex gap-2">
            <select name="month" class="form-select" onchange="this.form.submit()">
                @for ($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                @endfor
            </select>
            <select name="year" class="form-select" onchange="this.form.submit()">
                @for ($i = 2023; $i <= 2026; $i++)
                    <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
            <a href="{{ route('owner.download.monthly.sales.report', ['month' => $month, 'year' => $year]) }}" class="btn btn-outline-primary">
                <i class="bi bi-download me-2"></i> Unduh PDF
            </a>
        </form>
    </div>

    <div class="card">
        <div class="card-body">
            <h4>LAPORAN KOMISI BULANAN</h4>
            <p>Bulan: {{ date('F', mktime(0, 0, 0, $month, 1)) }}</p>
            <p>Tahun: {{ $year }}</p>
            <p>Tanggal cetak: {{ $tanggalCetak }}</p>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Kode Produk</th>
                        <th>Nama Produk</th>
                        <th>Harga Jual</th>
                        <th>Tanggal Masuk</th>
                        <th>Tanggal Laku</th>
                        <th>Komisi Hunter</th>
                        <th>Komisi ReUse Mart</th>
                        <th>Bonus Penitip</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($formattedData as $item)
                        <tr>
                            <td>{{ $item['kode_produk'] }}</td>
                            <td>{{ $item['nama_produk'] }}</td>
                            <td>{{ $item['harga_jual'] }}</td>
                            <td>{{ $item['tanggal_masuk'] }}</td>
                            <td>{{ $item['tanggal_laku'] }}</td>
                            <td>{{ $item['komisi_hunter'] }}</td>
                            <td>{{ $item['komisi_reuse_mart'] }}</td>
                            <td>{{ $item['bonus_penitip'] }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="2"><strong>Total</strong></td> <!-- Kolom 1 dan 2 digabung -->
                        <td>{{ number_format($totalHargaJual, 0, ',', '.') }}</td> <!-- Total Harga Jual -->
                        <td></td> <!-- Kosongkan Tanggal Masuk -->
                        <td></td> <!-- Kosongkan Tanggal Laku -->
                        <td>{{ number_format($totalKomisiHunter, 0, ',', '.') }}</td>
                        <td>{{ number_format($totalKomisiReUseMart, 0, ',', '.') }}</td>
                        <td>{{ number_format($totalBonusPenitip, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table th, .table td {
        text-align: center;
        vertical-align: middle;
    }
    .form-select {
        padding: 8px;
        border-radius: 6px;
    }
</style>
@endpush