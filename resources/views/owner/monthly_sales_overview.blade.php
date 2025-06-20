@extends('owner.owner_layout')

@section('title', 'Laporan Penjualan Bulanan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">Laporan Penjualan Bulanan</h2>
            <p class="text-muted">ReUse Mart, Jl. Green Eco Park No. 456 Yogyakarta</p>
        </div>
        <form method="GET" action="{{ route('owner.monthly.sales.overview') }}" class="d-flex gap-2">
            <input type="text" name="date" class="form-control" value="{{ $date }}" placeholder="Pilih tahun (YYYY)" onchange="this.form.submit()">
            <a href="{{ route('owner.download.monthly.sales.overview', ['date' => $date]) }}" class="btn btn-outline-primary">
                <i class="bi bi-download me-2"></i> Unduh PDF
            </a>
        </form>
    </div>

    <div class="card">
        <div class="card-body">
            <h4>LAPORAN PENJUALAN BULANAN</h4>
            <p>Tanggal cetak: {{ \Carbon\Carbon::now()->format('d F Y') }}</p>
            <p>Tahun: {{ $date }}</p>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Bulan</th>
                        <th>Jumlah Barang Terjual</th>
                        <th>Jumlah Penjualan Kotor</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allMonths as $item)
                        <tr>
                            <td>{{ $item['bulan'] }}</td>
                            <td>{{ $item['barang_terjual'] }}</td>
                            <td>{{ $item['penjualan_kotor'] }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td><strong>Total</strong></td>
                        <td><strong>{{ $totalBarang }}</strong></td>
                        <td><strong>{{ number_format($totalPenjualan, 0, ',', '.') }}</strong></td>
                    </tr>
                </tbody>
            </table>

            <!-- Grafik (gunakan Chart.js untuk web) -->
            <canvas id="salesChart" width="400" height="200"></canvas>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($allMonths->pluck('bulan')),
            datasets: [{
                label: 'Jumlah Penjualan Kotor',
                data: @json($allMonths->pluck('penjualan_kotor_raw')),
                backgroundColor: 'rgba(0, 177, 79, 0.6)',
                borderColor: 'rgba(0, 177, 79, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                        }
                    }
                }
            }
        }
    });
</script>
@endpush