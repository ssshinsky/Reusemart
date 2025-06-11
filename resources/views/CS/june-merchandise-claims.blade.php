@extends('CS.dashboard')

@section('title', '100 Point Merchandise Claims')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

@if (session('success'))
    <script>
        Swal.fire({
            title: 'Sukses!',
            text: '{{ session('success') }}',
            icon: 'success',
            confirmButtonColor: '#28a745',
            confirmButtonText: 'OK'
        }).then(() => {
            location.reload();
        });
    </script>
@endif
@if (session('error'))
    <script>
        Swal.fire({
            title: 'Error!',
            text: '{{ session('error') }}',
            icon: 'error',
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'OK'
        });
    </script>
@endif

<h2>100 Point Merchandise Claims</h2>

<div style="margin: 1rem 0; display: flex; gap: 1rem; align-items: center;">
    <button class="btn-action" id="editToggle">📅 Aktifkan Isi Tanggal</button>
    <span id="statusMessage" style="color: #dc3545; font-size: 14px; display: none;"></span>
</div>

<div style="margin-bottom: 1rem; display: flex; gap: 0.5rem; align-items: center;">
    <input type="text" id="searchInput" placeholder="🔍 Search June Claims" class="input-search" style="width: 100%;">
</div>

<div class="table-container">
    <div class="table-scroll-x">
        <table class="table-penitip">
            <thead>
                <tr>
                    <th class="col-id">
                        <a href="?sort_by=id_transaksi_merchandise&sort_dir={{ $sortBy == 'id_transaksi_merchandise' && $sortDir == 'asc' ? 'desc' : 'asc' }}">ID
                            @if($sortBy == 'id_transaksi_merchandise')
                                <span>{{ $sortDir == 'asc' ? '▲' : '▼' }}</span>
                            @endif
                        </a>
                    </th>
                    <th class="col-merch">Merchandise</th>
                    <th class="col-pembeli">Pembeli</th>
                    <th class="col-jumlah">
                        <a href="?sort_by=jumlah&sort_dir={{ $sortBy == 'jumlah' && $sortDir == 'asc' ? 'desc' : 'asc' }}">Jumlah
                            @if($sortBy == 'jumlah')
                                <span>{{ $sortDir == 'asc' ? '▲' : '▼' }}</span>
                            @endif
                        </a>
                    </th>
                    <th class="col-poin">
                        <a href="?sort_by=total_poin_penukaran&sort_dir={{ $sortBy == 'total_poin_penukaran' && $sortDir == 'asc' ? 'desc' : 'asc' }}">Total Poin
                            @if($sortBy == 'total_poin_penukaran')
                                <span>{{ $sortDir == 'asc' ? '▲' : '▼' }}</span>
                            @endif
                        </a>
                    </th>
                    <th class="col-klaim">
                        <a href="?sort_by=tanggal_klaim&sort_dir={{ $sortBy == 'tanggal_klaim' && $sortDir == 'asc' ? 'desc' : 'asc' }}">Tanggal Klaim
                            @if($sortBy == 'tanggal_klaim')
                                <span>{{ $sortDir == 'asc' ? '▲' : '▼' }}</span>
                            @endif
                        </a>
                    </th>
                    <th class="col-ambil">Tanggal Ambil</th>
                    <th class="col-status">Status</th>
                    <th class="col-action sticky-action header-action">Aksi</th>
                </tr>
            </thead>
            <tbody id="claimTableBody">
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
            </tbody>
        </table>
    </div>
</div>

<script>
    function rebindEditToggle() {
        const actionCells = document.querySelectorAll('.action-cell');
        const headerAction = document.querySelector('.header-action');
        const toggleBtn = document.getElementById('editToggle');
        const statusMessage = document.getElementById('statusMessage');
        const isActive = toggleBtn?.classList.contains('active');

        if (isActive) {
            headerAction.style.display = 'table-cell';
            actionCells.forEach(cell => cell.classList.add('visible'));
            document.querySelectorAll('.form-update-ambil .date-input').forEach(input => input.style.display = 'inline-block');
            document.querySelectorAll('.form-update-ambil .fill-date-btn').forEach(btn => btn.style.display = 'none');
            document.querySelectorAll('.form-update-ambil .ok-btn').forEach(btn => btn.style.display = 'inline-block');
            statusMessage.style.display = 'none';
        } else {
            headerAction.style.display = 'none';
            actionCells.forEach(cell => cell.classList.remove('visible'));
            document.querySelectorAll('.form-update-ambil .date-input').forEach(input => input.style.display = 'none');
            document.querySelectorAll('.form-update-ambil .fill-date-btn').forEach(btn => btn.style.display = 'inline-block');
            document.querySelectorAll('.form-update-ambil .ok-btn').forEach(btn => btn.style.display = 'none');
        }
    }

    const toggleButton = document.getElementById('editToggle');
    toggleButton.addEventListener('click', function () {
        toggleButton.classList.toggle('active');
        rebindEditToggle();
    });

    const searchInput = document.getElementById('searchInput');
    let timeout = null;

    searchInput.addEventListener('input', function () {
        clearTimeout(timeout);
        const query = this.value;

        timeout = setTimeout(() => {
            fetch(`{{ route('cs.merchandise-claim.search') }}?q=${encodeURIComponent(query)}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.text())
            .then(html => {
                const tbody = document.getElementById('claimTableBody');
                if (tbody) {
                    tbody.innerHTML = html;
                    rebindEditToggle();
                    bindFillDateButtons();
                }
            })
            .catch(err => console.error('Live search error:', err));
        }, 300);
    });

    function bindFillDateButtons() {
        document.querySelectorAll('.fill-date-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const form = this.closest('.form-update-ambil');
                const input = form.querySelector('.date-input');
                const okBtn = form.querySelector('.ok-btn');
                input.style.display = 'inline-block';
                this.style.display = 'none';
                okBtn.style.display = 'inline-block';
                input.focus();
            });
        });

        document.querySelectorAll('.form-update-ambil').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const id = this.getAttribute('data-id');
                const tanggal = this.querySelector('input[name="tanggal_ambil_merch"]').value;
                const statusMessage = document.getElementById('statusMessage');

                if (!tanggal) {
                    statusMessage.textContent = 'Tanggal ambil wajib diisi!';
                    statusMessage.style.display = 'block';
                    return;
                }

                Swal.fire({
                    title: 'Confirm Update?',
                    text: `Set tanggal ambil untuk claim ID ${id} ke ${tanggal}?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#aaa',
                    confirmButtonText: 'Yes, update!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    } else {
                        const input = this.querySelector('.date-input');
                        const fillBtn = this.querySelector('.fill-date-btn');
                        input.style.display = 'none';
                        fillBtn.style.display = 'inline-block';
                        this.querySelector('.ok-btn').style.display = 'none';
                    }
                });
            });
        });
    }

    document.addEventListener('DOMContentLoaded', bindFillDateButtons);
</script>

<style>
body, h2, input, button, table, th, td, span {
    font-family: 'Poppins', sans-serif;
}
.input-search {
    flex: 1;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 16px;
}
.table-container {
    margin-top: 1rem;
    width: 100%;
    border: 1px solid #ccc;
    border-radius: 6px;
    overflow-x: auto;
    max-height: 500px;
}
.table-penitip {
    width: max-content;
    min-width: 100%;
    border-collapse: collapse;
    background-color: #F9FAFB;
}
.table-penitip th,
.table-penitip td {
    padding: 12px;
    height: 50px;
    border: 1px solid #ddd;
    white-space: nowrap;
}
.table-penitip th {
    background-color: #A7F3D0;
    text-align: center;
    font-weight: 600;
}
.table-penitip th a {
    color: #333;
    text-decoration: none;
}
.table-penitip th a:hover {
    text-decoration: underline;
}
.center { text-align: center; }
.col-id      { width: 50px; }
.col-merch   { width: 200px; }
.col-pembeli { width: 200px; }
.col-jumlah  { width: 80px; }
.col-poin    { width: 100px; }
.col-klaim   { width: 120px; }
.col-ambil   { width: 120px; }
.col-status  { width: 100px; }
.col-action  {
    width: 180px;
    position: sticky;
    right: 0;
    background: #fff;
    z-index: 1;
}
.sticky-action {
    position: sticky;
    right: 0;
    background-color: #A7F3D0;
}
.action-cell {
    position: sticky;
    right: 0;
    background: #fff;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    padding: 8px;
}
.action-cell.visible {
    display: flex;
    border: none;
}
.date-input {
    padding: 6px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 14px;
    width: 140px;
    transition: border-color 0.3s;
}
.date-input:focus {
    border-color: #4CAF50;
    outline: none;
}
.action-btn {
    padding: 6px 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 4px;
    transition: background-color 0.2s ease-in-out;
}
.fill-date-btn {
    background-color: #4CAF50;
    color: white;
}
.fill-date-btn:hover {
    background-color: #45a049;
}
.ok-btn {
    background-color: #28a745;
    color: white;
}
.ok-btn:hover {
    background-color: #218838;
}
.action-text {
    color: #888;
    font-size: 14px;
    text-align: center;
    width: 100%;
}
.btn-action {
    padding: 10px 20px;
    border: 1px solid #ccc;
    border-radius: 6px;
    background: white;
    cursor: pointer;
    font-weight: 500;
}
.btn-action.active {
    background-color: #DCDCDC;
}
.btn-action:hover {
    background-color: #EAEAEA;
}
</style>
@endsection