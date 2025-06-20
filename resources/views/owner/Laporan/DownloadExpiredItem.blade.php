<!DOCTYPE html>
<html>
<head>
    <title>Laporan Barang Habis Masa Penitipan</title>
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
    </style>
</head>
<body>
    <div class="header">
        <h1 class="text-center">Laporan Barang yang Masa Penitipannya Sudah Habis</h1>
        <h2 class="text-center">ReUse Mart</h2>
        <p class="text-center">Jl. Green Eco Park No. 456 Yogyakarta</p> 
        <p class="text-left">Tanggal Cetak: {{ $reportDate }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Kode Produk</th> 
                <th>Nama Produk</th> 
                <th>Id Penitip</th> 
                <th>Nama Penitip</th> 
                <th>Tanggal Masuk</th> 
                <th>Tanggal Akhir</th> 
                <th>Batas Ambil</th> 
            </tr>
        </thead>
        <tbody>
            @forelse ($expiredItems as $item)
                <tr>
                    <td>{{ $item->kode_barang }}</td>
                    <td>{{ $item->nama_barang }}</td>
                    <td>{{ $item->transaksiPenitipan->penitip->id_penitip ?? 'N/A' }}</td>
                    <td>{{ $item->transaksiPenitipan->penitip->nama_penitip ?? 'N/A' }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->transaksiPenitipan->tanggal_penitipan)->format('d/m/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->transaksiPenitipan->tanggal_berakhir)->format('d/m/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->batas_pengambilan)->format('d/m/Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">Tidak ada barang yang masa penitipan awalnya sudah habis dan belum terjual/didonasikan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>