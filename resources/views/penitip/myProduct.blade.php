@foreach ($products as $product)
    <div class="card">
        <img src="{{ asset('storage/' . $product->gambar->first()->nama_file ?? 'default.jpg') }}"
            alt="{{ $product->nama_barang }}">
        <h3>{{ $product->nama_barang }}</h3>
        <p>Status:
            @if ($product->status_barang === 'AVAILABLE')
                <span class="text-green-500">Available</span>
            @elseif($product->status_barang === 'SOLD OUT')
                <span class="text-red-500">Sold Out</span>
            @elseif($product->status_barang === 'DONATED')
                <span class="text-yellow-500">Donated</span>
            @elseif($product->status_barang === 'COLLECTED')
                <span class="text-blue-500">Collected</span>
            @endif
        </p>
    </div>
@endforeach
