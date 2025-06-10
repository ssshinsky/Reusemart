<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            size: landscape;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: left;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 14px;
        }
        p{
            font-size:12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 12px;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .footer {
            text-align: left;
            margin-top: 20px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Donasi Barang</h2>
        <p>Tahun: 2025</p>
        <p>ReUse Mart, Jl. Babarsari No.44Janti, Caturtunggal, Depok, Sleman, DIY 55281</p>
    </div>

    <div>
        <p>Tanggal cetak: {{ \Carbon\Carbon::now()->format('j F Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Kode Produk</th>
                <th>Nama Produk</th>
                <th>ID Penitip</th>
                <th>Nama Penitip</th>
                <th>Tanggal Donasi</th>
                <th>Organisasi</th>
                <th>Nama Penerima</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($donations as $donation)
                <tr>
                    <td>{{ $donation->barang->kode_barang ?? 'N/A' }}</td>
                    <td>{{ $donation->barang->nama_barang ?? 'N/A' }}</td>
                    <td>{{ $donation->transaksiPenitipan->id_penitip ?? 'N/A' }}</td>
                    <td>{{ $donation->transaksiPenitipan->penitip->nama_penitip ?? 'N/A' }}</td>
                    <td>{{ \Carbon\Carbon::parse($donation->tanggal_donasi)->format('d/m/Y') }}</td>
                    <td>{{ $donation->requestDonasi->organisasi->nama_organisasi ?? 'N/A' }}</td>
                    <td>{{ $donation->nama_penerima }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Tidak ada data donasi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>