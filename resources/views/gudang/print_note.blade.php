<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Penitipan Barang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 10pt;
        }
        .container {
            width: 100%;
            max-width: 500px;
            margin: 20px auto;
            border: 1px solid #000;
            padding: 15px;
        }
        .header {
            text-align: center;
            margin-bottom: 15px; /* Jarak lebih besar biar rapi */
        }
        .header h1 {
            margin: 0;
            font-size: 14pt;
            font-weight: bold;
        }
        .header p {
            margin: 3px 0; /* Jarak antar alamat sedikit diperbesar */
            font-size: 9pt;
            color: #000;
            font-weight: normal; /* Tidak bold */
        }
        hr {
            border: 0;
            border-top: 1px solid #000;
            margin: 8px 0; /* Jarak HR lebih rapi */
        }
        .info-table {
            width: 100%;
            margin-bottom: 10px; /* Jarak antar tabel lebih besar */
        }
        .info-table td {
            padding: 2px 0; /* Padding lebih besar biar ga mepet */
            font-size: 9pt;
            vertical-align: top;
        }
        .info-table .label {
            width: 110px;
            font-weight: normal; /* Tidak bold kecuali yang ditentukan */
        }
        .info-table .label-bold {
            font-weight: bold; /* Khusus untuk yang perlu bold */
        }
        .barang-table {
            width: 100%;
            margin-bottom: 10px; /* Jarak antar barang lebih rapi */
        }
        .barang-table td {
            padding: 2px 0; /* Jarak lebih besar biar ga mepet */
            font-size: 9pt;
        }
        .barang-table .label {
            width: 160px;
            font-weight: normal; /* Tidak bold */
        }
        .spacer {
            margin-bottom: 15px; /* Jarak besar antara Delivery dan barang */
        }
        @page {
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>ReuseMart</h1>
            <p>Jl. Green Eco Park No. 456 Yogyakarta</p>
            <hr>
        </div>

        <table class="info-table">
            <tr><td class="label">No Nota</td><td>: {{ $formattedData['no_nota'] }}</td></tr>
            <tr><td class="label">Tanggal penitipan</td><td>: {{ $formattedData['tanggal_penitipan'] }}</td></tr>
            <tr><td class="label">Masa penitipan sampai</td><td>: {{ $formattedData['masa_penitipan'] }}</td></tr>
        </table>

        <table class="info-table">
            <tr><td class="label label-bold">Penitip</td><td>: {{ $formattedData['penitip_nama'] }}</td></tr>
            <tr><td class="label"></td><td>{{ $formattedData['penitip_alamat'] }}</td></tr>
            <tr><td class="label">Delivery</td><td>: {{ $formattedData['delivery'] }}</td></tr>
        </table>

        <div class="spacer"></div> <!-- Jarak antara Delivery dan barang -->

        @foreach ($formattedData['barang_list'] as $item)
            <table class="barang-table">
                <tr><td class="label">{{ $item['nama'] }}</td><td>{{ $item['harga'] }}</td></tr>
                @if ($item['garansi'])
                    <tr><td class="label">{{ $item['garansi'] }}</td><td></td></tr>
                @endif
                <tr><td class="label">Berat barang</td><td>: {{ $item['berat'] }}</td></tr>
            </table>
        @endforeach

        <table class="info-table">
            <tr><td class="label">Diterima dan QC oleh:</td><td></td></tr>
            <tr><td class="label"></td><td>{{ $formattedData['qc_kode'] }} - {{ $formattedData['qc_nama'] }}</td></tr>
        </table>
    </div>
</body>
</html>