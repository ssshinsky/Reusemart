{{-- Modal detail --}}
<div class="modal fade" id="detailModal{{ $product->id_barang }}" tabindex="-1" aria-labelledby="detailLabel{{ $product->id_barang }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailLabel{{ $product->id_barang }}">Detail Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    {{-- Gambar --}}
                    <div class="col-md-5">
                        <div id="carouselGambar{{ $product->id_barang }}" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                @foreach ($product->gambar as $index => $gambar)
                                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                        <img src="{{ asset('storage/gambar_barang/' . $gambar->gambar_barang) }}"
                                            class="d-block w-100 rounded shadow"
                                            style="height: 300px; object-fit: contain;"
                                            alt="{{ $product->nama_barang }}">
                                    </div>
                                @endforeach
                            </div>

                            @if ($product->gambar->count() > 1)
                                <button class="carousel-control-prev" type="button" data-bs-target="#carouselGambar{{ $product->id_barang }}" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Previous</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#carouselGambar{{ $product->id_barang }}" data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Next</span>
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- Detail --}}
                    <div class="col-md-7">
                        <h5 class="fw-bold">{{ $product->nama_barang }}</h5>
                        <p><strong>Kode Barang:</strong> {{ $product->kode_barang }}</p>
                        <p><strong>Kategori:</strong> {{ $product->kategori->nama_kategori ?? '-' }}</p>
                        <p><strong>Harga:</strong> Rp {{ number_format($product->harga_barang, 0, ',', '.') }}</p>
                        <p><strong>Berat:</strong> {{ $product->berat_barang }} kg</p>
                        <p><strong>Status:</strong> {{ $product->status_barang }}</p>
                        <p><strong>Deskripsi:</strong><br>{{ $product->deskripsi_barang }}</p>

                        @if ($product->id_kategori == 1)
                            <p><strong>Garansi:</strong> {{ $product->status_garansi }}</p>
                            @if(strtolower($product->status_garansi) === 'warranty')
                                <p><strong>Tanggal Garansi Berakhir:</strong> 
                                    {{ \Carbon\Carbon::parse($product->tanggal_garansi)->translatedFormat('d F Y') }}
                                </p>
                            @endif
                        @endif

                        <p><strong>Perpanjangan:</strong> 
                            {{ $product->perpanjangan == 1 ? 'Extended' : 'Not Extended' }}
                        </p>

                        @php
                            use Illuminate\Support\Carbon;

                            $tanggalBerakhir = optional($product)->tanggal_berakhir;
                            $hariIni = now();
                            $hMinus3 = $tanggalBerakhir ? Carbon::parse($tanggalBerakhir)->subDays(3) : null;

                            $bolehPerpanjang = strtolower($product->status_barang) === 'available' &&
                                            $product->perpanjangan == 0 &&
                                            $tanggalBerakhir &&
                                            $hariIni->greaterThanOrEqualTo($hMinus3);
                        @endphp

                        @if ($bolehPerpanjang)
                            <form action="{{ route('penitip.perpanjang', $product->id_barang) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-warning text-dark mt-3">
                                    🔁 Perpanjang Penitipan (+30 Hari)
                                </button>
                            </form>
                        @endif

                        @if(in_array($product->status_barang, ['Available', 'Awaiting Owner Pickup']))
                            <button class="btn btn-primary" onclick="confirmPickup({{ $product->id_barang }})">
                                Pick Up Item
                            </button>
                        @endif

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
