@foreach($transaksiMerchandises as $transaksi)
<tr>
    <td class="center">{{ $transaksi->id_transaksi_merchandise }}</td>
    <td>{{ $transaksi->merchandise->nama_merch }}</td>
    <td>{{ $transaksi->pembeli->nama_pembeli }}</td>
    <td class="center">{{ $transaksi->jumlah }}</td>
    <td class="center">{{ $transaksi->total_poin_penukaran }}</td>
    <td class="center">{{ $transaksi->tanggal_klaim }}</td>
    <td class="center">{{ $transaksi->tanggal_ambil_merch ?? 'Belum Diambil' }}</td>
    <td class="center">{{ $transaksi->status_transaksi }}</td>
    <td class="action-cell">
        @if($transaksi->status_transaksi !== 'diambil')
        <form action="{{ route('cs.merchandise-claim.update', $transaksi->id_transaksi_merchandise) }}" method="POST" class="form-update-ambil" data-id="{{ $transaksi->id_transaksi_merchandise }}" style="display: inline-flex; align-items: center; gap: 4px;">
            @csrf @method('PUT')
            <input type="date" name="tanggal_ambil_merch" value="{{ $transaksi->tanggal_ambil_merch ?? '' }}" class="date-input" style="display: none; padding: 6px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px;" min="{{ $transaksi->tanggal_klaim }}">
            <button type="button" class="action-btn fill-date-btn" data-id="{{ $transaksi->id_transaksi_merchandise }}">📅 Isi Tanggal Ambil</button>
            <button type="submit" class="action-btn ok-btn" style="display: none; background-color: #28a745; color: white;">✅ OK</button>
        </form>
        @else
        <span class="action-text">Sudah Diambil</span>
        @endif
    </td>
</tr>
@endforeach