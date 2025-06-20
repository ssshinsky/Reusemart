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
        .note {
            font-style: italic;
            font-size: 12px;
            color: #e74c3c; /* Merah lembut untuk peringatan */
            text-align: right;
            margin-right: 20px;
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
        .logo-placeholder {
            text-align: center;
            margin-bottom: 20px;
            color: #27ae60;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <!-- <div class="logo-placeholder">[Logo ReUse Mart Placeholder]</div> Placeholder logo, bisa diganti dengan gambar kalau ada -->
        <h2>Laporan Stok Gudang</h2>
        <h3>ReUse Mart, Jl. Green Eco Park No. 456 Yogyakarta</h3>
        <!-- <p>LAPORAN STOK GUDANG</p> -->
        <p>Tanggal cetak: {{ $date }}</p>
        <p class="note">Stok yang bisa dilihat adalah stok per hari ini (sama dengan tanggal cetak). Tidak bisa dilihat stok yang kemarin-kemarin.</p>
    </div>

    <table>
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
</body>
</html>