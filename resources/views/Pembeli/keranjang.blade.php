@extends('layouts.main')

@section('content')
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <h2 class="mb-4">Shopping Cart</h2>

    @if ($items->isEmpty())
        <div class="alert alert-info">
            Keranjang Anda kosong. <a href="{{ route('produk.allproduct') }}">Belanja sekarang!</a>
        </div>
    @else
        <form method="POST" action="{{ route('cart.checkout') }}">
            @csrf
            <!-- Daftar Item di Keranjang -->
            <div class="table-responsive mb-4">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Select</th>
                            <th>Image</th>
                            <th>Product Name</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $item)
                            <tr>
                                <td>
                                    <input type="checkbox" name="selected_items[]" class="select-item"
                                        value="{{ $item->id_item_keranjang }}"
                                        data-harga="{{ $item->barang->harga_barang }}"
                                        {{ $item->is_selected ? 'checked' : '' }}>
                                </td>
                                <td style="width: 100px;">
                                    @if ($item->barang->gambar->isNotEmpty())
                                        <img src="{{ asset('storage/gambar/' . $item->barang->gambar->first()->gambar_barang) }}"
                                            alt="{{ $item->barang->nama_barang }}" class="img-fluid rounded"
                                            style="max-width: 80px;">
                                    @else
                                        <img src="{{ asset('images/placeholder.jpg') }}" alt="No Image"
                                            class="img-fluid rounded" style="max-width: 80px;">
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $item->barang->nama_barang }}</strong><br>
                                    <small>{{ $item->barang->berat_barang }} Kg </small>
                                </td>
                                <td>
                                    IDR {{ number_format($item->barang->harga_barang, 0, ',', '.') }}
                                </td>
                                <td>
                                    <div class="text-center">1</div>
                                </td>
                                <td>
                                    IDR {{ number_format($item->barang->harga_barang, 0, ',', '.') }}
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#deleteModal{{ $item->id_item_keranjang }}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Shipping Method -->
            <div class="mb-3">
                <label for="metode_pengiriman" class="form-label fw-bold">Shipping Method</label>
                <select class="form-select" name="metode_pengiriman" id="metode_pengiriman" required>
                    <option value="">-- Select Method --</option>
                    <option value="kurir">Courier (Yogyakarta only)</option>
                    <option value="ambil">Self Pickup</option>
                </select>
                @error('metode_pengiriman')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <!-- Shipping Address -->
            <div class="mb-3" id="alamat_pengiriman" style="display: none;">
                <label for="id_alamat" class="form-label fw-bold">Select Delivery Address</label>
                <select class="form-select" name="id_alamat" id="id_alamat">
                    @if ($alamatPembeli->isEmpty())
                        <option value="">No address available. Please add an address first.</option>
                    @else
                        @foreach ($alamatPembeli as $alamat)
                            <option value="{{ $alamat->id_alamat }}">{{ $alamat->alamat_lengkap }}</option>
                        @endforeach
                    @endif
                </select>
                @error('id_alamat')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <!-- Price Summary -->
            <div class="card p-3 mb-3 shadow-sm">
                <h5 class="card-title">Price Summary</h5>
                <p><strong>Subtotal:</strong> IDR <span
                        id="subtotal-text">{{ number_format($totalHarga, 0, ',', '.') }}</span></p>
                <p><strong>Shipping Fee:</strong>
                    <span id="ongkir-text">
                        {{ $totalHarga >= 1500000 ? 'Free' : 'IDR ' . number_format(100000, 0, ',', '.') }}
                    </span>
                </p>
                <hr>
                <p class="fs-5"><strong>Total:</strong> IDR <span id="total-text">
                        {{ number_format($totalHarga >= 1500000 ? $totalHarga : $totalHarga + 100000, 0, ',', '.') }}
                    </span></p>
            </div>

            <!-- Checkout Button -->
            <div class="text-end">
                <button type="submit" class="btn btn-success btn-lg" id="checkout-button">
                    Proceed to Payment
                </button>
            </div>
        </form>
    @endif

    @foreach ($items as $item)
        <!-- Modal Konfirmasi Hapus -->
        <div class="modal fade" id="deleteModal{{ $item->id_item_keranjang }}" tabindex="-1"
            aria-labelledby="deleteModalLabel{{ $item->id_item_keranjang }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="deleteModalLabel{{ $item->id_item_keranjang }}">Konfirmasi Hapus</h5>
                        <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Yakin ingin menghapus <strong>{{ $item->barang->nama_barang }}</strong> dari keranjang?
                    </div>
                    <div class="modal-footer">
                        <form action="{{ route('cart.remove', $item->id_item_keranjang) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection

@push('scripts')
    <script>
        function formatRupiah(angka) {
            return angka.toLocaleString('id-ID');
        }

        function updateTotal() {
            let checkboxes = document.querySelectorAll('.select-item:checked');
            let subtotal = 0;

            checkboxes.forEach(cb => {
                let harga = parseInt(cb.dataset.harga);
                subtotal += harga; // Kuantitas = 1
            });

            let ongkir = subtotal >= 1500000 ? 0 : 100000;
            let total = subtotal + ongkir;

            document.getElementById('subtotal-text').innerText = formatRupiah(subtotal);
            document.getElementById('ongkir-text').innerText = ongkir === 0 ? 'Gratis' : 'IDR ' + formatRupiah(ongkir);
            document.getElementById('total-text').innerText = formatRupiah(total);
        }

        document.querySelectorAll('.select-item').forEach(cb => {
            cb.addEventListener('change', updateTotal);
        });

        document.getElementById('metode_pengiriman').addEventListener('change', function() {
            let alamat = document.getElementById('alamat_pengiriman');
            let alamatSelect = document.querySelector('select[name="id_alamat"]');
            alamat.style.display = (this.value === 'kurir') ? 'block' : 'none';
            alamatSelect.required = (this.value === 'kurir');
        });

        // Validasi sebelum submit
        document.getElementById('checkout-button').addEventListener('click', function(e) {
            let checkboxes = document.querySelectorAll('.select-item:checked');
            if (checkboxes.length === 0) {
                e.preventDefault();
                alert('Pilih setidaknya satu item untuk checkout.');
            }
        });

        // Panggil updateTotal saat halaman dimuat
        updateTotal();
    </script>
@endpush