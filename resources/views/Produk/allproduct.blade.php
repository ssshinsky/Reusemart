<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>All Products - ReUseMart</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        h3 {
            font-weight: 600;
            color: #2e7d32;
        }

        .card {
            border: 1px solid #e0e0e0;
            transition: transform 0.2s;
        }

        .card:hover {
            transform: scale(1.03);
        }

        .btn-success {
            background-color: #388e3c;
            border: none;
        }

        .btn-success:hover {
            background-color: #2e7d32;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    @include('partials.navbar')

    <div class="container py-4">
        <h3 class="mb-4 text-center">All Products</h3>
        <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-3">
            @forelse ($produk as $item)
                <div class="col">
                    <div class="card h-100 text-center p-2">
                        <img src="{{ asset('storage/gambar_barang/' . ($item->gambar->first()->gambar_barang ?? 'default.png')) }}"
                            class="card-img-top" alt="{{ $item->nama_barang }}">
                        <div class="card-body d-flex flex-column">
                            <p class="card-title small">{{ $item->nama_barang }}</p>
                            <p class="fw-bold text-success">Rp{{ number_format($item->harga_barang, 0, ',', '.') }}</p>
                            <div class="mt-auto">
                                <a href="{{ route('umum.show', $item->id_barang) }}" class="btn btn-success btn-sm w-100">View Details</a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-center text-muted">No products available.</p>
            @endforelse
        </div>
    </div>

    @include('partials.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>