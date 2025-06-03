<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Detail Barang - ReuseMart</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #00b14f;
            --text-dark: #212529;
            --text-muted: #6c757d;
            --bg-light: #f8f9fa;
            --border-color: #dee2e6;
            --star-color: #ffd700;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            font-size: 0.875rem;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 1.5rem 1rem;
            flex: 1 0 auto;
        }

        .text-primary {
            color: var(--primary-color) !important;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #019944;
            border-color: #019944;
            transform: scale(1.03);
        }

        .btn-outline {
            background-color: transparent;
            border: 1px solid var(--border-color);
            border-radius: 50%;
            padding: 0.4rem;
            transition: all 0.3s ease;
        }

        .btn-outline:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        .card {
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1rem;
            background-color: white;
            transition: box-shadow 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.08);
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-bottom: 1rem;
        }

        .breadcrumb a {
            color: var(--text-dark);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .breadcrumb a:hover {
            color: var(--primary-color);
        }

        .slider {
            position: relative;
            overflow: hidden;
            width: 100%;
            max-height: 300px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
        }

        .slider-container {
            display: flex;
            transition: transform 0.5s ease-in-out;
            width: 100%;
        }

        .slide {
            min-width: 100%;
            height: 300px;
            object-fit: contain;
            border-radius: 12px;
        }

        .slider-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            padding: 0.5rem;
            cursor: pointer;
            z-index: 10;
            font-size: 0.75rem;
            transition: background-color 0.3s ease;
        }

        .slider-btn:hover {
            background-color: var(--primary-color);
        }

        .slider-btn.prev {
            left: 0;
            border-radius: 0 6px 6px 0;
        }

        .slider-btn.next {
            right: 0;
            border-radius: 6px 0 0 6px;
        }

        .dots {
            position: absolute;
            bottom: 0.5rem;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 0.4rem;
        }

        .dot {
            width: 8px;
            height: 8px;
            background-color: #d1d5db;
            border-radius: 50%;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .dot.active {
            background-color: var(--primary-color);
        }

        .product-info {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .product-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-dark);
        }

        .product-price {
            font-size: 1.125rem;
            font-weight: 600;
            color: #dc2626;
        }

        .product-description {
            font-size: 0.875rem;
            color: var(--text-muted);
            line-height: 1.5;
        }

        .warranty-box {
            background-color: var(--bg-light);
            padding: 0.75rem;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            font-size: 0.75rem;
        }

        .rating-box {
            background-color: #fff;
            padding: 1rem;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            font-size: 0.75rem;
            transition: transform 0.2s ease, box-shadow 0.3s ease;
        }

        .rating-box:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .star-rating, .star-partial, .star-empty {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            position: relative;
        }

        .star-rating svg, .star-partial svg, .star-empty svg {
            width: 1rem;
            height: 1rem;
            position: absolute;
            top: 0;
            left: 0;
        }

        .star-rating .star-foreground {
            fill: var(--star-color);
        }

        .star-partial .star-background {
            fill: #d1d5db;
        }

        .star-partial .star-foreground {
            fill: var(--star-color);
            clip-path: inset(0 calc(100% - var(--partial-width, 0%)) 0 0);
        }

        .star-empty .star-background {
            fill: #d1d5db;
        }

        .penitip-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            background: linear-gradient(135deg, var(--primary-color), #019944);
            color: white;
            font-weight: 600;
            font-size: 0.85rem;
            transition: transform 0.2s ease, box-shadow 0.3s ease;
            margin-top: 0.5rem;
        }

        .penitip-badge:hover {
            transform: scale(1.05);
            box-shadow: 0 2px 8px rgba(0, 177, 79, 0.3);
        }

        .penitip-name {
            font-weight: 600;
            text-transform: capitalize;
        }

        .discussion-section {
            margin-top: 1.5rem;
        }

        .discussion-form textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            resize: vertical;
            font-size: 0.875rem;
            color: var(--text-dark);
            transition: border-color 0.3s ease;
        }

        .discussion-form textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(0, 177, 79, 0.1);
        }

        .discussion-item {
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background-color: var(--bg-light);
            transition: box-shadow 0.3s ease;
        }

        .discussion-item:hover {
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.05);
        }

        .discussion-item.admin {
            margin-left: 1rem;
            background-color: #e6f0ff;
            border-color: #bfdbfe;
        }

        @media (max-width: 768px) {
            .grid-cols-3 {
                grid-template-columns: 1fr;
            }

            .slider {
                max-height: 200px;
            }

            .slide {
                height: 200px;
            }

            .product-title {
                font-size: 1rem;
            }

            .product-price {
                font-size: 1rem;
            }

            .btn-primary {
                padding: 0.4rem 0.8rem;
                font-size: 0.75rem;
            }

            .star-rating, .star-partial, .star-empty {
                width: 1rem;
                height: 1rem;
            }

            .penitip-badge {
                font-size: 0.75rem;
                padding: 0.3rem 0.8rem;
            }
        }

        footer {
            background-color: #000;
            color: #fff;
            text-align: center;
            padding: 1.5rem 0;
            margin-top: auto;
        }
    </style>
</head>
<body>
    @include('partials.navbar')

    <main class="flex-grow-1">
        <div class="container">
            <!-- Breadcrumb -->
            <nav class="breadcrumb">
                <a href="/">Home</a>
                <span class="text-gray-400">/</span>
                <a href="/kategori/{{ $barang->kategori->id_kategori }}">{{ $barang->kategori->nama_kategori }}</a>
                <span class="text-gray-400">/</span>
                <span>{{ $barang->nama_barang }}</span>
            </nav>

            <!-- Main Content -->
            <div class="card grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Slider Gambar -->
                <div class="col-span-2">
                    <div class="slider">
                        <div class="slider-container" id="slider-container">
                            @foreach($barang->gambar as $gambar)
                                <img src="{{ asset('storage/gambar/' . $gambar->gambar_barang) }}" alt="Slide" class="slide">
                            @endforeach
                        </div>
                        <button class="slider-btn prev" onclick="moveSlide(-1)">❮</button>
                        <button class="slider-btn next" onclick="moveSlide(1)">❯</button>
                        <div class="dots" id="dots">
                            @for($i = 0; $i < $barang->gambar->count(); $i++)
                                <span class="dot @if($i == 0) active @endif" onclick="currentSlide({{ $i + 1 }})"></span>
                            @endfor
                        </div>
                    </div>
                </div>

                <!-- Info Barang -->
                <div class="product-info">
                    <h1 class="product-title">{{ $barang->nama_barang }}</h1>
                    <p class="product-price">Rp {{ number_format($barang->harga_barang, 0, ',', '.') }}</p>
                    <p class="product-description">{{ $barang->deskripsi_barang }}</p>
                    <div class="warranty-box">
                        <p class="font-medium">
                            Garansi:
                            @if($statusGaransi === 'garansi')
                                @if($garansiBerlaku)
                                    <span class="text-primary font-semibold">Masih berlaku hingga {{ \Carbon\Carbon::parse($barang->tanggal_garansi)->format('d M Y') }}</span>
                                @else
                                    <span class="text-red-600 font-semibold">Sudah habis (sampai {{ \Carbon\Carbon::parse($barang->tanggal_garansi)->format('d M Y') }})</span>
                                @endif
                            @else
                                <span class="text-muted font-medium">Tidak ada garansi</span>
                            @endif
                        </p>
                    </div>
                    <div class="rating-box">
                        @php
                            $penitip = optional($barang->transaksiPenitipan)->penitip;
                            $rating = $penitip ? ($penitip->rata_rating ?? 0) : 0;
                            $fullStars = floor($rating);
                            $decimal = $rating - $fullStars;
                            $partialWidth = min(max($decimal * 100, 0), 100) . '%';
                        @endphp
                        @if($penitip && $rating >= 0)
                            <div class="d-flex align-items-center gap-1 mb-2">
                                @for($i = 0; $i < $fullStars; $i++)
                                    <span class="star-rating">
                                        <svg viewBox="0 0 24 24">
                                            <path class="star-foreground" d="M12 .587l3.668 7.431 8.332 1.151-6.001 5.843 1.417 8.264L12 18.839l-7.416 3.897 1.417-8.264-6.001-5.843 8.332-1.151z"/>
                                        </svg>
                                    </span>
                                @endfor
                                @if($decimal > 0)
                                    <span class="star-partial" style="--partial-width: {{ $partialWidth }}">
                                        <svg viewBox="0 0 24 24">
                                            <path class="star-background" d="M12 .587l3.668 7.431 8.332 1.151-6.001 5.843 1.417 8.264L12 18.839l-7.416 3.897 1.417-8.264-6.001-5.843 8.332-1.151z"/>
                                            <path class="star-foreground" d="M12 .587l3.668 7.431 8.332 1.151-6.001 5.843 1.417 8.264L12 18.839l-7.416 3.897 1.417-8.264-6.001-5.843 8.332-1.151z"/>
                                        </svg>
                                    </span>
                                @endif
                                @for($i = 0; $i < (5 - $fullStars - ($decimal > 0 ? 1 : 0)); $i++)
                                    <span class="star-empty">
                                        <svg viewBox="0 0 24 24">
                                            <path class="star-background" d="M12 .587l3.668 7.431 8.332 1.151-6.001 5.843 1.417 8.264L12 18.839l-7.416 3.897 1.417-8.264-6.001-5.843 8.332-1.151z"/>
                                        </svg>
                                    </span>
                                @endfor
                                <span class="text-primary font-semibold">({{ number_format($rating, 1) }}/5)</span>
                            </div>
                            <div class="penitip-badge">
                                <i class="bi bi-person-fill me-1"></i>
                                <span class="penitip-name">{{ $penitip->nama_penitip ?? 'N/A' }}</span>
                            </div>
                        @else
                            <p class="text-muted">Rating dan data penitip tidak tersedia.</p>
                        @endif
                    </div>
                    @auth
                    <form action="{{ route('cart.add') }}" method="POST" class="flex items-center gap-3">
                        @csrf
                        <input type="hidden" name="id_barang" value="{{ $barang->id_barang }}">
                        <button type="submit" class="btn-primary">Add to Cart</button>
                        <button type="button" class="btn-outline">
                            <i class="bi bi-heart w-5 h-5"></i>
                        </button>
                    </form>
                    @else
                    <p class="text-red-500 text-xs">Silakan login untuk membeli barang ini.</p>
                    @endauth
                </div>
            </div>
                                    <span class="text-muted font-medium">Tidak ada garansi</span>
                                @endif
                            </p>
                        </div>
                        <form id="add-to-cart-form" action="{{ route('cart.add', ['id' => $barang->id_barang]) }}"
                            method="POST" class="flex items-center gap-3">
                            @csrf
                            <input type="hidden" name="id_barang" value="{{ $barang->id_barang }}">
                            <button type="submit" class="btn btn-primary" id="add-to-cart-btn">Tambah ke Keranjang</button>
                            <button type="button" class="btn btn-outline" title="Tambahkan ke wishlist">
                                <i class="fa-solid fa-heart"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Diskusi Produk -->
                <div class="discussion-section card">
                    <h2 class="text-base font-semibold mb-3 border-b border-gray-200 pb-1">Diskusi Produk</h2>
                    @auth('api_pembeli')
                        <form action="{{ route('diskusi.store') }}" method="POST" class="discussion-form mb-4">
                            @csrf
                            <input type="hidden" name="id_barang" value="{{ $barang->id_barang }}">
                            <textarea name="diskusi" rows="3" placeholder="Tulis pertanyaan Anda..." required></textarea>
                            @error('diskusi')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <button type="submit" class="btn-primary mt-3">Kirim Pertanyaan</button>
                        </form>
                    @else
                        <p class="text-muted mb-4 text-sm">Silakan login sebagai pembeli untuk mengajukan pertanyaan.</p>
                    @endauth
                    @if ($diskusi->isEmpty())
                        <p class="text-muted text-sm">Belum ada diskusi untuk produk ini.</p>
                    @else
                        <div class="space-y-3">
                            @foreach ($diskusi as $item)
                                <div class="discussion-item @if ($item->id_pegawai) admin @endif">
                                    <p class="font-semibold text-sm">
                                        @if ($item->id_pembeli)
                                            Q: {{ $item->pembeli->nama_pembeli }}
                                        @else
                                            A: {{ $item->pegawai->nama_pegawai }}
                                        @endif
                                    </p>
                                    <p class="text-muted text-sm mt-1">{{ $item->diskusi }}</p>
                                    <p class="text-muted text-xs mt-1">
                                        {{ $item->created_at ? $item->created_at->format('d M Y, H:i') : 'Tanggal tidak tersedia' }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </main>

        <!-- Modal Sukses -->
        <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="successModalLabel">Berhasil</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p id="successModalMessage"></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Error -->
        <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="errorModalLabel">Error</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p id="errorModalMessage"></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @section('scripts')
        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            let slideIndex = 0;
            const slides = document.getElementsByClassName("slide");
            const dots = document.getElementsByClassName("dot");

            function showSlide(index) {
                if (index >= slides.length) slideIndex = 0;
                if (index < 0) slideIndex = slides.length - 1;
                const offset = -slideIndex * 100;
                document.getElementById("slider-container").style.transform = `translateX(${offset}%)`;
                updateDots();
            }

            function moveSlide(direction) {
                slideIndex += direction;
                showSlide(slideIndex);
            }

            function currentSlide(index) {
                slideIndex = index - 1;
                showSlide(slideIndex);
            }

            function updateDots() {
                for (let i = 0; i < dots.length; i++) {
                    dots[i].classList.remove("active");
                }
                dots[slideIndex].classList.add("active");
            }

            setInterval(() => moveSlide(1), 5000);
            showSlide(slideIndex);

            // AJAX untuk menambah ke keranjang
            document.getElementById('add-to-cart-form').addEventListener('submit', async function(e) {
                e.preventDefault();
                const form = this;
                const button = form.querySelector('#add-to-cart-btn');
                button.disabled = true;

                try {
                    const response = await axios.post(form.action, {
                        _token: form.querySelector('input[name="_token"]').value,
                        id_barang: form.querySelector('input[name="id_barang"]').value
                    });

                    // Tampilkan modal sukses
                    document.getElementById('successModalMessage').textContent = response.data.message;
                    const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                    successModal.show();
                } catch (error) {
                    // Tampilkan modal error
                    const message = error.response?.data?.message ||
                        'Terjadi kesalahan saat menambahkan ke keranjang.';
                    document.getElementById('errorModalMessage').textContent = message;
                    const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                    errorModal.show();
                } finally {
                    button.disabled = false;
                }
            });
        </script>
    @endsection
</body>
</html>