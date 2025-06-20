<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penjualan per Kategori</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
        }
        .header h1 {
            margin: 0;
            font-size: 16pt;
        }
        .header h2 {
            margin: 0;
            font-size: 14pt;
        }
        .header p {
            margin: 0;
            font-size: 10pt;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            text-align: center;
        }
        .text-center {
            text-align: center;
        }
        .text-end {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="text-center">Laporan Penjualan per Kategori Barang</h1>
        <h2 class="text-center">ReUse Mart</h2>
        <p class="text-center">Jl. Green Eco Park No. 456 Yogyakarta</p> 
        <p class="text-left">Tahun: {{ $year }}</p>
        <p class="text-left">Tanggal Cetak: {{ $reportDate }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Kategori</th>
                <th>Jumlah Item Terjual</th>
                <th>Jumlah Item Gagal Terjual</th>
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
                    <td colspan="3" class="text-center">Tidak ada data penjualan untuk tahun ini.</td>
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
</body>
</html>