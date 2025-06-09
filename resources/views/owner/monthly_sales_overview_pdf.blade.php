<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f0f8f0;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #00b14f;
            padding-bottom: 10px;
        }
        h2, h4 {
            color: #00b14f;
            margin: 0;
        }
        h2 { font-size: 28px; font-weight: 600; }
        h4 { font-size: 20px; font-weight: 400; }
        p { margin: 5px 0; color: #2c3e50; }
        .d-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .form-control { padding: 8px; border-radius: 6px; }
        .btn-outline-primary { padding: 8px 16px; border-radius: 6px; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
            vertical-align: middle;
        }
        th {
            background-color: #00b14f;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
        }
        tr:nth-child(even) { background-color: #e8f5e9; }
        .chart-container { margin-top: 20px; text-align: center; }
    </style>
</head>
<body>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">Laporan Penjualan Bulanan</h2>
            <p class="text-muted">ReUse Mart, Jl. Green Eco Park No. 456 Yogyakarta</p>
        </div>
        <!-- <form method="GET" action="{{ route('owner.monthly.sales.overview') }}" class="d-flex gap-2">
            <input type="text" name="date" class="form-control" value="{{ $date }}" placeholder="Pilih tahun (YYYY)" disabled>
            <span class="btn btn-outline-primary disabled">
                <i class="bi bi-download me-2"></i> Unduh PDF
            </span> -->
        </form>
    </div>

    <div class="card">
        <div class="card-body">
            <!-- <h4>LAPORAN PENJUALAN BULANAN</h4> -->
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

            <div class="chart-container">
                @if($chartImage)
                    <img src="data:image/png;base64,{{ $chartImage }}" alt="Grafik Penjualan" width="400" height="200">
                @else
                    <p>[Grafik Tidak Tersedia]</p>
                @endif
            </div>
        </div>
    </div>
</body>
</html>