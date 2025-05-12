<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Detail Barang - ReuseMart</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <style>
    .thumbnail:hover {
      transform: scale(1.05);
      transition: transform 0.3s ease;
    }
    .card-hover:hover {
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
      transition: box-shadow 0.3s ease;
    }
    .image-main {
      transition: opacity 0.3s ease;
    }
    .image-main:hover {
      opacity: 0.95;
    }
    .slider {
      position: relative;
      overflow: hidden;
      width: 100%;
      max-height: 450px;
    }
    .slider-container {
      display: flex;
      transition: transform 0.5s ease-in-out;
      width: 100%;
    }
    .slide {
      min-width: 100%;
      height: 450px;
      object-fit: contain;
    }
    .slider-btn {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      background-color: rgba(0, 0, 0, 0.5);
      color: white;
      border: none;
      padding: 1rem;
      cursor: pointer;
      z-index: 10;
    }
    .slider-btn.prev {
      left: 0;
    }
    .slider-btn.next {
      right: 0;
    }
    .dots {
      position: absolute;
      bottom: 10px;
      left: 50%;
      transform: translateX(-50%);
      display: flex;
      gap: 8px;
    }
    .dot {
      width: 10px;
      height: 10px;
      background-color: #d1d5db;
      border-radius: 50%;
      cursor: pointer;
    }
    .dot.active {
      background-color: #10b981;
    }
  </style>
</head>
<body class="bg-green-50 font-sans text-gray-800">

  <!-- Container -->
  <div class="max-w-7xl mx-auto px-4 py-8">

    <!-- Breadcrumb -->
    <nav class="text-sm mb-8 flex items-center space-x-2 text-gray-600">
      <a href="/" class="hover:text-green-600 font-medium transition-colors">Home</a>
      <span class="text-gray-400">/</span>
      <a href="/kategori/{{ $barang->kategori->id_kategori }}" class="hover:text-green-600 font-medium transition-colors">{{ $barang->kategori->nama_kategori }}</a>
      <span class="text-gray-400">/</span>
      <span class="font-semibold text-gray-800">{{ $barang->nama_barang }}</span>
    </nav>

    <!-- Main Content -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-10 bg-white rounded-2xl shadow-lg p-8 card-hover">

      <!-- Slider Gambar -->
      <div class="col-span-2">
        <div class="slider">
          <div class="slider-container" id="slider-container">
            @foreach($barang->gambar as $gambar)
              <img src="{{ asset('storage/gambar/' . $gambar->gambar_barang) }}" alt="Slide" class="slide rounded-xl border-2 border-gray-200">
            @endforeach
          </div>
          <button class="slider-btn prev" onclick="moveSlide(-1)">&#10094;</button>
          <button class="slider-btn next" onclick="moveSlide(1)">&#10095;</button>
          <div class="dots" id="dots">
            @for($i = 0; $i < $barang->gambar->count(); $i++)
              <span class="dot @if($i == 0) active @endif" onclick="currentSlide({{ $i + 1 }})"></span>
            @endfor
          </div>
        </div>
      </div>

      <!-- Info Barang -->
      <div class="space-y-6">
        <h1 class="text-4xl font-extrabold text-gray-900">{{ $barang->nama_barang }}</h1>

        <p class="text-3xl font-bold text-red-600">Rp {{ number_format($barang->harga_barang, 0, ',', '.') }}</p>

        <p class="text-gray-700 text-lg leading-relaxed">{{ $barang->deskripsi_barang }}</p>

        <!-- Garansi -->
        <div class="bg-gray-100 p-4 rounded-xl border border-gray-200">
          <p class="font-semibold text-base text-gray-800">
            Garansi:
            @if($statusGaransi === 'garansi')
              @if($garansiBerlaku)
                <span class="text-green-600 font-semibold">Masih berlaku hingga {{ \Carbon\Carbon::parse($barang->tanggal_garansi)->format('d M Y') }}</span>
              @else
                <span class="text-red-600 font-semibold">Sudah habis (sampai {{ \Carbon\Carbon::parse($barang->tanggal_garansi)->format('d M Y') }})</span>
              @endif
            @else
              <span class="text-gray-500 font-medium">Tidak ada garansi</span>
            @endif
          </p>
        </div>

        <!-- Add to Cart -->
        @auth
        <form action="{{ route('cart.add') }}" method="POST" class="flex items-center space-x-4">
          @csrf
          <input type="hidden" name="id_barang" value="{{ $barang->id_barang }}">
          <button type="submit" class="bg-pink-500 hover:bg-pink-600 text-white px-8 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105">
            Add to Cart
          </button>
          <button type="button" class="bg-white border-2 border-gray-300 rounded-full p-3 hover:bg-gray-100 transition-all duration-300">
            <svg class="w-6 h-6 text-gray-600 hover:text-pink-500 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
          </button>
        </form>
        @else
        <p class="text-red-500 text-base mt-3">Silakan login untuk membeli barang ini.</p>
        @endauth
      </div>
    </div>

    <!-- Diskusi Produk -->
    <div class="mt-12 bg-white rounded-2xl shadow-lg p-8">
      <h2 class="text-2xl font-bold mb-6 text-gray-900 border-b-2 border-green-200 pb-2">Diskusi Produk</h2>

      @auth('api_pembeli')
      <form action="{{ route('diskusi.store') }}" method="POST" class="mb-8">
        @csrf
        <input type="hidden" name="id_barang" value="{{ $barang->id_barang }}">
        <textarea name="diskusi" rows="4" class="w-full p-4 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-green-300 placeholder-gray-400 text-gray-800 font-medium" placeholder="Tulis pertanyaan Anda..." required></textarea>
        @error('diskusi')
          <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
        @enderror
        <button type="submit" class="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105">
          Kirim Pertanyaan
        </button>
      </form>
      @else
      <p class="text-gray-600 mb-6 text-lg">Silakan login sebagai pembeli untuk mengajukan pertanyaan.</p>
      @endauth

      @if($diskusi->isEmpty())
      <p class="text-gray-500 text-lg">Belum ada diskusi untuk produk ini.</p>
      @else
      <div class="space-y-6">
        @foreach($diskusi as $item)
          <div class="bg-gray-50 border-2 border-gray-200 p-5 rounded-xl @if($item->id_pegawai) ml-12 bg-blue-50 border-blue-200 @endif card-hover">
            <p class="font-semibold text-gray-900">
              @if($item->id_pembeli)
                Q: {{ $item->pembeli->nama_pembeli }}
              @else
                A: {{ $item->pegawai->nama_pegawai }}
              @endif
            </p>
            <p class="text-gray-700 mt-2">{{ $item->diskusi }}</p>
            <p class="text-gray-500 text-sm mt-1">
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

    // Auto-slide bang di set
    setInterval(() => moveSlide(1), 5000);

    // Inisialisasi slide pertama
    showSlide(slideIndex);
  </script>

</body>
</html>