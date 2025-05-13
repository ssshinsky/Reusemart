<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Detail Barang - ReuseMart</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #00b14f;
            --text-dark: #212529;
            --text-muted: #6c757d;
            --bg-light: #f8f9fa;
            --border-color: #dee2e6;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            font-size: 0.875rem;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 1.5rem 1rem;
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
        }
    </style>
</head>
<body>
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
            @if($diskusi->isEmpty())
            <p class="text-muted text-sm">Belum ada diskusi untuk produk ini.</p>
            @else
            <div class="space-y-3">
                @foreach($diskusi as $item)
                    <div class="discussion-item @if($item->id_pegawai) admin @endif">
                        <p class="font-semibold text-sm">
                            @if($item->id_pembeli)
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
    </script>
</body>
</html>