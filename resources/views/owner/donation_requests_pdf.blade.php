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
            font-size: 12px;
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
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Donasi Barang</h2>
        <p>Tahun: 2025</p>
        <p>ReUse Mart, Jl. Babarsari No.44 Janti, Caturtunggal, Depok, Sleman, DIY 55281</p>
    </div>

    <div>
        <p>Tanggal cetak: {{ \Carbon\Carbon::now()->format('j F Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID Request</th>
                <th>Organisasi</th>
                <th>Alamat</th>
                <th>Request</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($requests as $request)
                    <tr>
                        <td>{{ $request->id_request }}</td>
                        <td>{{ $request->organisasi->nama_organisasi ?? 'N/A' }}</td>
                        <td>{{ $request->organisasi->alamat ?? 'N/A' }}</td>
                        <td>{{ $request->request }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="no-data">Tidak ada request donasi.</td>
                    </tr>
                @endforelse
        </tbody>
    </table>
</body>
</html>