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
            background-color: #f0f8f0; /* Hijau muda */
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #27ae60; /* Hijau tua ReUse Mart */
            padding-bottom: 10px;
        }
        h2 {
            color: #27ae60; /* Hijau tua */
            font-size: 28px;
            margin: 0;
            font-weight: 600;
        }
        h3 {
            color: #27ae60; /* Hijau tua, konsisten */
            font-size: 20px;
            margin: 5px 0;
            font-weight: 400;
        }
        p {
            margin: 5px 0;
            color: #2c3e50; /* Abu-abu tua */
        }
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
            background-color: #27ae60; /* Hijau ReUse Mart */
            color: white;
            font-weight: 600;
            text-transform: uppercase;
        }
        tr:nth-child(even) {
            background-color: #e8f5e9; /* Hijau sangat muda */
        }
        tr:hover {
            background-color: #d5f5d5; /* Hijau lebih terang saat hover */
        }
        .total-row td {
            font-weight: bold;
            background-color: #d5f5d5; /* Highlight total */
        }
    </style>
</head>
<body>
    <div class="header">
        <!-- <div class="logo-placeholder">[Logo ReUse Mart Placeholder]</div> Placeholder logo, bisa diganti dengan gambar kalau ada -->
        <h2>Laporan Komisi Bulanan per Produk</h2>
        <h3>ReUse Mart, Jl. Green Eco Park No. 456 Yogyakarta</h3>
        <!-- <p>LAPORAN KOMISI BULANAN</p> -->
        <p>Bulan: {{ date('F', mktime(0, 0, 0, $month, 1)) }}</p>
        <p>Tahun: {{ $year }}</p>
        <p>Tanggal cetak: {{ $tanggalCetak }}</p>
    </div>

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
                <td colspan="2">Total</td>
                <td>{{ number_format($totalHargaJual, 0, ',', '.') }}</td>
                <td></td>
                <td></td>
                <td>{{ number_format($totalKomisiHunter, 0, ',', '.') }}</td>
                <td>{{ number_format($totalKomisiReUseMart, 0, ',', '.') }}</td>
                <td>{{ number_format($totalBonusPenitip, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>