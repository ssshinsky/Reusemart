@extends('layouts.main')

@section('content')

    <body>
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
                                @foreach ($barang->gambar as $gambar)
                                    <img src="{{ asset('storage/gambar/' . $gambar->gambar_barang) }}" alt="Slide"
                                        class="slide">
                                @endforeach
                            </div>
                            <button class="slider-btn prev" onclick="moveSlide(-1)">❮</button>
                            <button class="slider-btn next" onclick="moveSlide(1)">❯</button>
                            <div class="dots" id="dots">
                                @for ($i = 0; $i < $barang->gambar->count(); $i++)
                                    <span class="dot @if ($i == 0) active @endif"
                                        onclick="currentSlide({{ $i + 1 }})"></span>
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
                                @if ($statusGaransi === 'garansi')
                                    @if ($garansiBerlaku)
                                        <span class="text-primary font-semibold">Masih berlaku hingga
                                            {{ \Carbon\Carbon::parse($barang->tanggal_garansi)->format('d M Y') }}</span>
                                    @else
                                        <span class="text-red-600 font-semibold">Sudah habis (sampai
                                            {{ \Carbon\Carbon::parse($barang->tanggal_garansi)->format('d M Y') }})</span>
                                    @endif
                                @else
                                    <span class="text-muted font-medium">Tidak ada garansi</span>
                                @endif
                            </p>
                        </div>
                        {{-- @auth --}}
                        <form action="{{ route('cart.add', ['id' => $barang->id_barang]) }}" method="POST"
                            class="flex items-center gap-3">
                            @csrf
                            <input type="hidden" name="id_barang" value="{{ $barang->id_barang }}">

                            <button type="submit" class="btn btn-primary">
                                Tambah ke Keranjang
                            </button>

                            <button type="button" class="btn btn-outline" title="Tambahkan ke wishlist">
                                <i class="fa-solid fa-heart"></i>
                            </button>
                        </form>
                        {{-- @else
                        <p class="text-red-500 text-sm mt-2">Silakan login untuk membeli barang ini.</p>
                    @endauth --}}
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
    @endsection

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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
