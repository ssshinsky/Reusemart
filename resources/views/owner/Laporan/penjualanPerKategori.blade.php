@extends('owner.owner_layout')

@section('title', 'Laporan Penjualan per Kategori Barang')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">Laporan Penjualan per Kategori Barang</h2>
            <p class="text-muted">Tanggal Hari Ini: {{ $reportDate }}</p>
        </div>
        <div>
            <a href="{{ route('owner.reports.download_sales_by_category') }}" class="btn btn-primary">
                <i class="bi bi-download me-2"></i> Unduh PDF
            </a>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th scope="col">Kategori</th>
                        <th scope="col" class="text-center">Jumlah Item Terjual</th>
                        <th scope="col" class="text-center">Jumlah Item Gagal Terjual</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalTerjual = 0;
                        $totalGagalTerjual = 0;
                    @endphp
                    @forelse ($salesData as $data)
                        <tr>
                            <td>{{ $data['kategori'] }}</td>
                            <td class="text-center">{{ $data['jumlah_terjual'] }}</td>
                            <td class="text-center">{{ $data['jumlah_gagal_terjual'] }}</td>
                        </tr>
                        @php
                            $totalTerjual += $data['jumlah_terjual'];
                            $totalGagalTerjual += $data['jumlah_gagal_terjual'];
                        @endphp
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">Tidak ada data penjualan untuk tahun ini.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th class="text-end">Total</th>
                        <th class="text-center">{{ $totalTerjual }}</th>
                        <th class="text-center">{{ $totalGagalTerjual }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection