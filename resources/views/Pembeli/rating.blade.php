@extends('layouts.main')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav class="breadcrumb mb-4">
        <a href="/" class="text-gray-600 hover:text-primary">Home</a>
        <span class="text-gray-400 mx-2">/</span>
        <a href="{{ route('pembeli.purchase') }}" class="text-gray-600 hover:text-primary">Riwayat Pembelian</a>
        <span class="text-gray-400 mx-2">/</span>
        <span class="text-gray-800">Rating Transaksi #{{ $transaksi->id_pembelian }}</span>
    </nav>

    <!-- Header -->
    <h2 class="mb-4 text-success fw-bold border-bottom pb-2" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.1);">
        Beri Rating Transaksi #{{ $transaksi->id_pembelian }}
    </h2>

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Main Card -->
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-body p-4 bg-light">
            <div class="mb-4">
                <p class="text-muted">
                    <span class="fw-medium">Tanggal:</span> {{ $transaksi->created_at->format('d M Y') }}<br>
                    <span class="fw-medium">Total:</span> <span class="text-success">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span><br>
                    <span class="fw-medium">Pengiriman:</span> {{ ucfirst($transaksi->metode_pengiriman) }}
                </p>
            </div>
            <h6 class="mt-3 text-primary font-semibold">Item untuk Dirating:</h6>
            <form action="{{ route('pembeli.rate', $transaksi->id_pembelian) }}" method="POST" id="ratingForm">
                @csrf
                @foreach ($transaksi->keranjang->detailKeranjang as $detail)
                    @if (is_null($detail->itemKeranjang->barang->rating))
                        <div class="mb-4 p-3 bg-white rounded-lg shadow-sm grid grid-cols-1 md:grid-cols-4 gap-4 items-center">
                            <!-- Gambar Barang -->
                            <div class="col-span-1">
                                @if ($detail->itemKeranjang->barang->gambar->isNotEmpty())
                                    <img src="{{ asset('storage/gambar/' . $detail->itemKeranjang->barang->gambar->first()->gambar_barang) }}"
                                        alt="{{ $detail->itemKeranjang->barang->nama_barang }}"
                                        class="w-full h-24 object-cover rounded-md">
                                @else
                                    <div class="w-full h-24 bg-gray-200 rounded-md flex items-center justify-center text-gray-500">
                                        Tidak ada gambar
                                    </div>
                                @endif
                            </div>
                            <!-- Nama, Detail, dan Rating -->
                            <div class="col-span-3">
                                <div class="relative group">
                                    <label class="form-label fw-bold text-gray-800 inline-block cursor-pointer hover:bg-green-100 transition-colors px-2 py-1 rounded">
                                        {{ $detail->itemKeranjang->barang->nama_barang }}
                                    </label>
                                    <div class="absolute hidden group-hover:block bg-gray-100 text-gray-800 text-xs rounded-lg p-2 mt-1 z-10 w-64 shadow-md">
                                        <p class="font-semibold">Detail Barang:</p>
                                        <p>Harga: Rp {{ number_format($detail->itemKeranjang->barang->harga_barang, 0, ',', '.') }}</p>
                                        @if ($detail->itemKeranjang->barang->deskripsi)
                                            <p>Deskripsi: {{ Str::limit($detail->itemKeranjang->barang->deskripsi_barang, 50) }}</p>
                                        @else
                                            <p>Deskripsi: Tidak ada deskripsi tersedia</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="star-rating mt-2" data-item-id="{{ $detail->itemKeranjang->id_barang }}">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="fa fa-star star text-muted" data-value="{{ $i }}"></i>
                                    @endfor
                                    <input type="hidden" name="ratings[{{ $detail->itemKeranjang->id_barang }}]" class="rating-input">
                                </div>
                                @error('ratings.' . $detail->itemKeranjang->id_barang)
                                    <div class="text-danger text-xs mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    @endif
                @endforeach
                <div class="flex gap-3 mt-4">
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-lg hover:bg-primary-dark transition">
                        Submit Rating
                    </button>
                    <a href="{{ route('pembeli.purchase') }}" class="btn btn-outline-secondary px-4 py-2 rounded-lg hover:bg-gray-100 transition">
                        Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- FontAwesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Style langsung -->
<style>
    .breadcrumb {
        font-size: 0.9rem;
        margin-bottom: 1rem;
    }
    .breadcrumb a {
        color: #6c757d;
        text-decoration: none;
    }
    .breadcrumb a:hover {
        color: #28a745;
    }
    .card {
        transition: all 0.3s ease;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
    }
    .fa-star {
        font-size: 1.5rem;
        cursor: pointer;
        margin-right: 8px;
        transition: color 0.2s, transform 0.2s;
    }
    .fa-star:hover {
        transform: scale(1.2);
    }
    .star-rating .star.active {
        color: #ffc107 !important;
    }
    .star-rating .star.hover {
        color: #ffca2c !important;
    }
    .btn-primary {
        background-color: #28a745;
        border: none;
    }
    .btn-primary:hover {
        background-color: #218838;
    }
    .btn-outline-secondary {
        border-color: #6c757d;
        color: #6c757d;
    }
    .btn-outline-secondary:hover {
        background-color: #f8f9fa;
    }
    @media (max-width: 768px) {
        .grid-cols-4 {
            grid-template-columns: 1fr;
        }
        .h-24 {
            height: 100px;
        }
    }
</style>

<!-- Script langsung -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log("DOM Loaded");

        document.querySelectorAll('.star-rating').forEach(ratingContainer => {
            const stars = ratingContainer.querySelectorAll('.star');
            const input = ratingContainer.querySelector('.rating-input');

            stars.forEach(star => {
                star.addEventListener('click', () => {
                    const value = parseInt(star.getAttribute('data-value'));
                    console.log("Star clicked:", value);
                    input.value = value;

                    stars.forEach(s => s.classList.remove('active'));
                    for (let i = 0; i < value; i++) {
                        stars[i].classList.add('active');
                    }
                });

                star.addEventListener('mouseover', () => {
                    const value = parseInt(star.getAttribute('data-value'));
                    stars.forEach((s, i) => {
                        s.classList.toggle('hover', i < value);
                    });
                });

                star.addEventListener('mouseout', () => {
                    stars.forEach(s => s.classList.remove('hover'));
                });
            });
        });

        document.getElementById('ratingForm').addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Yakin ingin submit rating?',
                text: 'Rating yang sudah disubmit tidak dapat diubah.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, submit!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    });
</script>
@endsection