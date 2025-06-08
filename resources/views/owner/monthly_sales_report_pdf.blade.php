<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        .total-row td {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h2 style="text-align: center;">Laporan Komisi Bulanan per Produk</h2>
    <h3 style="text-align: center;">ReUse Mart, Jl. Green Eco Park No. 456 Yogyakarta</h3>
    <p style="text-align: center;">LAPORAN KOMISI BULANAN</p>
    <p style="text-align: center;">Bulan: {{ date('F', mktime(0, 0, 0, $month, 1)) }}</p>
    <p style="text-align: center;">Tahun: {{ $year }}</p>
    <p style="text-align: center;">Tanggal cetak: {{ $tanggalCetak }}</p>

    <table>
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
            <tr class="total-row">
                <td colspan="2">Total</td> <!-- Kolom 1 dan 2 digabung -->
                <td>{{ number_format($totalHargaJual, 0, ',', '.') }}</td> <!-- Total Harga Jual -->
                <td></td> <!-- Kosongkan Tanggal Masuk -->
                <td></td> <!-- Kosongkan Tanggal Laku -->
                <td>{{ number_format($totalKomisiHunter, 0, ',', '.') }}</td>
                <td>{{ number_format($totalKomisiReUseMart, 0, ',', '.') }}</td>
                <td>{{ number_format($totalBonusPenitip, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>