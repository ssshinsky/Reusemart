<div class="tabs flex gap-4 mb-4">
    <a href="{{ route('penitip.transaction', ['filter' => 'all']) }}"
        class="{{ $filter == 'all' ? 'font-bold underline' : '' }}">All</a>
    <a href="{{ route('penitip.transaction', ['filter' => 'sold']) }}"
        class="{{ $filter == 'sold' ? 'font-bold underline' : '' }}">Sold product</a>
    <a href="{{ route('penitip.transaction', ['filter' => 'expired']) }}"
        class="{{ $filter == 'expired' ? 'font-bold underline' : '' }}">Expired product</a>
    <a href="{{ route('penitip.transaction', ['filter' => 'donated']) }}"
        class="{{ $filter == 'donated' ? 'font-bold underline' : '' }}">Donated items</a>
</div>

<div class="transaction-list">
    @forelse ($transaksis as $transaksi)
        <div class="border p-4 mb-2 rounded shadow">
            <h3 class="font-semibold">{{ $transaksi->barang->nama_barang }}</h3>
            <p>{{ $transaksi->barang->deskripsi }}</p>
            <p>Status: <span class="text-green-600">{{ $transaksi->status_transaksi }}</span></p>
            <p>Order Total: Rp{{ number_format($transaksi->total_harga, 0, ',', '.') }}</p>
        </div>
    @empty
        <p>Tidak ada transaksi pada kategori ini.</p>
    @endforelse
</div>
