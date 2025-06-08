<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laporan Transaksi Penitip</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: left;
            margin-bottom: 20px;
        }
        .header h3 {
            margin: 0;
            font-size: 16px;
        }
        .info {
            margin-bottom: 10px;
        }
        .info p {
            margin: 2px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 14px;
        }
        table th, table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }
        table th {
            background-color: #f3f3f3;
        }
        .text-left {
            text-align: left;
        }
        .total-row {
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="header">
        <h3>ReUse Mart</h3>
        <p>Jl. Green Eco Park No. 456 Yogyakarta</p>
    </div>

    <div class="info">
        <p><strong>LAPORAN TRANSAKSI PENITIP</strong></p>
        <p>ID Penitip : T{{ $penitip->id_penitip }}</p>
        <p>Nama Penitip : {{ $penitip->nama_penitip }}</p>
        <p>Bulan : {{ \Carbon\Carbon::create()->month($bulanLalu)->translatedFormat('F') }}</p>
        <p>Tahun : {{ $tahun }}</p>
        <p>Tanggal cetak : {{ \Carbon\Carbon::now()->format('d M Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Kode Produk</th>
                <th class="text-left">Nama Produk</th>
                <th>Tanggal Masuk</th>
                <th>Tanggal Laku</th>
                <th>Harga Jual Bersih <br> (sudah dipotong Komisi)</th>
                <th>Bonus Terjual Cepat</th>
                <th>Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalPendapatan = 0;
            @endphp

            @forelse ($penjualan as $item)
                @php
                    $hargaBersih = $item->harga_barang * 0.9; // contoh potong komisi 10%
                    $bonus = 30000; // misalnya semua dapat bonus terjual cepat
                    $pendapatan = $hargaBersih + $bonus;
                    $totalPendapatan += $pendapatan;
                @endphp
                <tr>
                    <td>{{ $item->kode_barang ?? '-' }}</td>
                    <td class="text-left">{{ $item->nama_barang ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal_masuk ?? $item->created_at)->format('d/m/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tanggal_terjual ?? $item->created_at)->format('d/m/Y') }}</td>
                    <td>Rp {{ number_format($hargaBersih, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($bonus, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($pendapatan, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Tidak ada transaksi bulan ini.</td>
                </tr>
            @endforelse

            <tr class="total-row">
                <td colspan="6">TOTAL</td>
                <td>Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
