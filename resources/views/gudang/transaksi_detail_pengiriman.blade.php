@extends('gudang.gudang_layout')

@section('title', 'Detail Transaksi Pengiriman')

@section('content')
<div class="container-fluid py-4">
    <h2 class="fw-bold mb-3 text-dark">
        <i class="fas fa-receipt me-2"></i> Transaction Detail #{{ $transaksi->id_pembelian }}
    </h2>

    <div class="card shadow-lg border-0 mb-4">
        <div class="card-body">
            <h5 class="fw-bold mb-3 text-primary">Buyer Information</h5>
            <p><strong>Name:</strong> {{ $transaksi->pembeli->nama_pembeli ?? '-' }}</p>
            <p><strong>Order Date:</strong> {{ \Carbon\Carbon::parse($transaksi->tanggal_pembelian)->translatedFormat('d F Y, H:i') }}</p>
            <p><strong>Shipping Method:</strong> {{ ucfirst($transaksi->metode_pengiriman) }}</p>
            <p><strong>Status:</strong> 
                <span class="badge bg-warning text-dark">{{ $transaksi->status_transaksi }}</span>
            </p>
        </div>
    </div>

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
                                <img src="{{ asset('storage/gambar_barang/' . $gambar->gambar_barang) }}" alt="Gambar Barang" class="img-fluid rounded mb-2" style="height: 120px; object-fit: cover;">
                            @endforeach
                            <p class="mb-1"><strong>Price:</strong> Rp{{ number_format($barang->harga_barang, 0, ',', '.') }}</p>
                            <p class="mb-1"><strong>Weight:</strong> {{ $barang->berat_barang }} kg</p>
                            @if ($barang->id_kategori == 1)
                                <p class="mb-1"><strong>Warranty Status:</strong> {{ $barang->status_garansi }}</p>
                            @endif
                            <p class="mb-1"><strong>Status:</strong> {{ $barang->status_barang }}</p> <!-- Menampilkan status -->
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Button for Create Invoice -->
    <div class="mt-3">
        @if($transaksi->metode_pengiriman == 'Courier')
            <a href="{{ route('gudang.transaksi.printInvoice', ['id' => $transaksi->id_pembelian]) }}" class="btn btn-primary">
                    <i class="fas fa-file-pdf me-2"></i> Create Invoice
            </a>
        @elseif($transaksi->metode_pengiriman == 'Self Pick-Up')
            <a href="{{ route('gudang.transaksi.printInvoicePickup', ['id' => $transaksi->id_pembelian]) }}" class="btn btn-primary">
                    <i class="fas fa-file-pdf me-2"></i> Create Invoice
            </a>
        @endif
    </div>
</div>
@endsection
