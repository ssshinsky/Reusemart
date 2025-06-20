<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $no_nota }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; }
        .details { margin-bottom: 20px; }
        .details p { margin: 5px 0; }
        .items-table { width: 100%; border-collapse: collapse; }
        .items-table th, .items-table td { border: 1px solid #000; padding: 8px; text-align: left; }
    </style>
</head>
<body>

    <div class="header">
        <h1>ReUseMart</h1>
        <p>Jl. Green Eco Park No. 456 Yogyakarta</p>
        <h2>Invoice #{{ $no_nota }}</h2>
    </div>

    <div class="details">
        <p><strong>Order Date:</strong> {{ $tanggal_pesan }}</p>
        <p><strong>Shipping Method:</strong> Courier</p>
        <p><strong>Delivered By:</strong> {{ $kurir }}</p>
    </div>

    <h3>Product List</h3>
    <table class="items-table">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Price</th>
                <th>Weight</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
                <tr>
                    <td>{{ $item['nama_barang'] }}</td>
                    <td>{{ $item['harga'] }}</td>
                    <td>{{ $item['berat'] }} kg</td>
                    <td>{{ $item['status'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p><strong>Subtotal:</strong> Rp{{ number_format($subtotal, 0, ',', '.') }}</p>
    <p><strong>Ongkir:</strong> Rp{{ number_format($ongkir, 0, ',', '.') }}</p>
    <p><strong>Total:</strong> Rp{{ number_format($total, 0, ',', '.') }}</p>

    <div class="footer">
        <p>Thank you for shopping with us!</p>
    </div>

</body>
</html>
