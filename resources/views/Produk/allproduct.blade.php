@section('content')
    @include('partials.navbar')

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

    <div class="container py-4">
        <h3 class="mb-4 text-center">All</h3>
        <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-3">
            @foreach ($produk as $item)
                <div class="col">
                    <div class="card h-100 text-center p-2">
                        @if ($item->gambar->count() > 0)
                            @foreach ($item->gambar as $gbr)
                                <img src="{{ asset('storage/' . $gbr->gambar_barang) }}" class="card-img-top mb-2"
                                    alt="{{ $item->nama_barang }}">
                            @endforeach
                        @else
                            <img src="{{ asset('storage/default.png') }}" class="card-img-top" alt="Default">
                        @endif
                        <div class="card-body">
                            <p class="card-title small">{{ $item->nama_barang }}</p>
                            <p class="fw-bold text-success">Rp{{ number_format($item->harga_barang, 0, ',', '.') }}</p>
                            <button class="btn btn-success btn-sm w-100">Buy Now</button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    @include('partials.footer')
@endsection
