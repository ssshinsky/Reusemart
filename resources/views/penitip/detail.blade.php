@include(layouts . main)

@section('content')
    @include(partials . navbar)
    <div class="container">
        <h2>Hasil Pencarian Transaksi</h2>
        @foreach ($transaksis as $transaksi)
            <tr>
                <td>{{ $transaksi->id_transaksi_penitipan }}</td>
                <td>{{ $transaksi->barang->nama_barang ?? '-' }}</td>
                <td>{{ $transaksi->created_at->format('d-m-Y') }}</td>
            </tr>
        @endforeach
    </div>
    @include(partials . footer)
@endsection
