@extends('owner.owner_layout')

@section('title', 'Laporan Stok Gudang')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">Laporan Stok Gudang</h2>
            <p class="text-muted">ReUse Mart, Jl. Green Eco Park No. 456 Yogyakarta</p>
        </div>
        <form method="GET" action="{{ route('owner.warehouse.stock.report') }}" class="d-flex gap-2">
            <!-- <input type="text" name="date" class="form-control" value="{{ $date }}" placeholder="Pilih tanggal (dd/mm/yyyy)" onchange="this.form.submit()"> -->
            <a href="{{ route('owner.download.warehouse.stock.report', ['date' => $date]) }}" class="btn btn-outline-primary">
                <i class="bi bi-download me-2"></i> Unduh PDF
            </a>
        </form>
    </div>

    <div class="card">
        <div class="card-body">
            <h4>LAPORAN STOK GUDANG</h4>
            <p>Tanggal cetak: {{ $date }}</p>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Kode Produk</th>
                        <th>Nama Produk</th>
                        <th>ID Penitip</th>
                        <th>Nama Penitip</th>
                        <th>Tanggal Masuk</th>
                        <th>Perpanjangan</th>
                        <th>ID Hunter</th>
                        <th>Nama Hunter</th>
                        <th>Harga</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($formattedData as $item)
                        <tr>
                            <td>{{ $item['kode_produk'] }}</td>
                            <td>{{ $item['nama_produk'] }}</td>
                            <td>{{ $item['id_penitip'] }}</td>
                            <td>{{ $item['nama_penitip'] }}</td>
                            <td>{{ $item['tanggal_masuk'] }}</td>
                            <td>{{ $item['perpanjangan'] }}</td>
                            <td>{{ $item['id_hunter'] }}</td>
                            <td>{{ $item['nama_hunter'] }}</td>
                            <td>{{ $item['harga'] }}</td>
                        </tr>
                    @endforeach
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
    .form-control {
        padding: 8px;
        border-radius: 6px;
    }
</style>
@endpush