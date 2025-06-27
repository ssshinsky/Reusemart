@forelse ($products as $product)
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card h-100 shadow-sm border-0 hover-shadow" 
            style="cursor: pointer;" 
            data-bs-toggle="modal" 
            data-bs-target="#detailModal{{ $product->id_barang }}">
            <img src="{{ asset('storage/gambar/' . ($product->gambar->first()->gambar_barang ?? 'default.jpg')) }}"
                class="card-img-top rounded-top" style="height: 220px; object-fit: cover;"
                alt="{{ $product->nama_barang }}">
            <div class="card-body">
                <h5 class="card-title text-truncate">{{ $product->nama_barang }}</h5>
                <p class="mb-0">
                    <small>Status: 
                        <span class="badge bg-{{ 
                            match(strtolower($product->status_barang)) {
                                'available' => 'success',
                                'sold' => 'danger',
                                'donated' => 'warning',
                                'returned' => 'primary',
                                'reserved' => 'secondary',
                                'Not Available' => 'dark',
                                default => 'dark'
                            }
                        }}">
                            {{ ucfirst($product->status_barang) }}
                        </span>
                    </small>
                </p>
            </div>
        </div>
    </div>

    @include('penitip.partials.modal', ['product' => $product])
@empty
    <div class="col-12">
        <div class="alert alert-info text-center">
            Belum ada produk tersedia.
        </div>
    </div>
@endforelse
