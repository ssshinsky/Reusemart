@extends('layouts.main')

@section('content')
<div class="container py-4">
    <h4 class="mb-4 fw-bold text-success">Riwayat Transaksi</h4>

    @if($riwayat->isEmpty())
        <div class="alert alert-warning">
            Anda belum pernah melakukan transaksi.
        </div>
    @else
        <div class="table-responsive card p-4 shadow-sm">
            <table class="table table-hover align-middle">
                <thead>
                    <tr class="table-success">
                        <th>No</th>
                        <th>No Nota</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Poin Diperoleh</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($riwayat as $i => $trans)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $trans->no_resi ?? 'N/A' }}</td>
                        <td>{{ \Carbon\Carbon::parse($trans->tanggal_pembelian)->format('d M Y') }}</td>
                        <td><span class="badge bg-{{ $trans->status_transaksi === 'Selesai' ? 'success' : 'secondary' }}">
                                {{ $trans->status_transaksi }}
                            </span></td>
                        <td>Rp {{ number_format($trans->total_harga, 0, ',', '.') }}</td>
                        <td>{{ $trans->poin_pembeli ?? 0 }}</td>
                        <td>
                            <a href="{{ route('pembeli.riwayat.detail', $trans->id_pembelian) }}"
                               class="btn btn-sm btn-outline-success">Detail</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection