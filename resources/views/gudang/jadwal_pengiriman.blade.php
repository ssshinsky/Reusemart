@extends('gudang.gudang_layout')

@section('title', 'Jadwalkan Pengiriman')

@section('content')
<div class="container-fluid py-4">
    @php
        $tanggal = \Carbon\Carbon::parse($transaksi->waktu_pembayaran);
        $noNota = $tanggal->format('y.m') . '.' . $transaksi->id_pembelian;
        $firstDetail = $transaksi->detailKeranjangs->first();
        $pembeli = $firstDetail->itemKeranjang->pembeli;
    @endphp
    <h2 class="fw-bold mb-3 text-dark">
        @if($transaksi->metode_pengiriman == 'Courier')
            <i class="fas fa-calendar-check me-2"></i> Schedule Delivery for Invoice #{{ $noNota}}
        @else
            <i class="fas fa-calendar-check me-2"></i> Schedule Pick Up for Invoice #{{ $noNota}}
        @endif
    </h2>

    <div class="card shadow-lg border-0 mb-4">
        <div class="card-body">
            <h5 class="fw-bold mb-3 text-primary">Buyer Information</h5>
            <p><strong>Name:</strong> {{ $pembeli->nama_pembeli ?? '-' }}</p>
            <p><strong>Order Date:</strong> {{ \Carbon\Carbon::parse($transaksi->tanggal_pembelian)->translatedFormat('d F Y, H:i') }}</p>
            <p><strong>Shipping Method:</strong> {{ ucfirst($transaksi->metode_pengiriman) }}</p>
            <p><strong>Status:</strong> 
                <span class="badge bg-warning text-dark">{{ $transaksi->status_transaksi }}</span>
            </p>
        </div>
    </div>

    @if(($transaksi->status_transaksi == 'Disiapkan' || $transaksi->status_transaksi == 'Preparing' || $transaksi->status_transaksi == 'Ready for Pickup') && $transaksi->tanggal_pengambilan == NULL)
    <form method="POST" action="{{ route('gudang.transaksi.jadwalkanPengiriman', $transaksi->id_pembelian) }}">
        @csrf
        <div class="card shadow-lg border-0 mb-4">
            <div class="card-body">
                <h5 class="fw-bold mb-3 text-primary">Schedule Information</h5>
                
                <div class="mb-3">
                    @if($transaksi->metode_pengiriman == 'Courier' || $transaksi->metode_pengiriman == 'Kurir')
                        <label for="tanggal_pengiriman" class="form-label">Delivery Date</label>
                        <input type="datetime-local" class="form-control" id="tanggal_pengiriman" name="tanggal_pengiriman" required>
                    @else
                        <label for="tanggal_pengiriman" class="form-label">Pick Up Date</label>
                        <input type="datetime-local" class="form-control" id="tanggal_pengiriman" name="tanggal_pengiriman" required>
                    @endif
                </div>

                @if(($transaksi->status_transaksi == 'Disiapkan' && $transaksi->metode_pengiriman == 'kurir') || ($transaksi->metode_pengiriman == 'Courier' && $transaksi->status_transaksi == 'Preparing' ))
                    <div class="mb-3">
                        <label for="id_kurir" class="form-label">Pilih Kurir</label>
                        <select class="form-control" id="id_kurir" name="id_kurir" required>
                            <option value="">Pilih Kurir</option>
                            @foreach ($kurirs as $kurir)
                                <option value="{{ $kurir->id_pegawai }}">{{ $kurir->nama_pegawai }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success">Schedule Delivery</button>
                @else
                    <button type="submit" class="btn btn-success">Schedule Pick Up</button>
                @endif
            </div>
        </div>
    </form>
    @endif

    <div class="card shadow-lg border-0">
        <div class="card-body">
            <h5 class="fw-bold mb-3 text-primary">Product List</h5>
            <div class="row g-4">
                @foreach ($transaksi->detailKeranjangs as $detail)
                    @php $barang = $detail->itemKeranjang->barang; @endphp
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="border rounded p-3 h-100 bg-light">
                            <h6 class="fw-bold text-dark mb-2">{{ $barang->nama_barang }}</h6>
                            @foreach ($barang->gambar->take(2) as $gambar)
                                <img src="{{ asset('storage/gambar/' . $gambar->gambar_barang) }}" alt="Gambar Barang" class="img-fluid rounded mb-2" style="height: 120px; object-fit: cover;">
                            @endforeach
                            <p class="mb-1"><strong>Price:</strong> Rp{{ number_format($barang->harga_barang, 0, ',', '.') }}</p>
                            <p class="mb-1"><strong>Weight:</strong> {{ $barang->berat_barang }} kg</p>
                            @if ($barang->id_kategori == 1)
                                <p class="mb-1"><strong>Warranty Status:</strong> {{ $barang->status_garansi }}</p>
                            @endif
                            <p class="mb-1"><strong>Status:</strong> {{ $barang->status_barang }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@section('scripts')
    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Pengiriman Berhasil Dijadwalkan!',
                text: '{{ session('success') }}',
                confirmButtonText: 'Tutup',
            });
        </script>
    @elseif(session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal Menjadwalkan Pengiriman',
                text: '{{ session('error') }}',
                confirmButtonText: 'Tutup',
            });
        </script>
    @endif
@endsection
@endsection
