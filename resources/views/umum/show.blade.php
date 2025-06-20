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
                                    <img src="{{ asset('storage/gambar_barang/' . ($barang->gambar->first()->gambar_barang ?? 'default.png')) }}" alt="Slide"
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