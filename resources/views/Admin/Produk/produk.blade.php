@extends('Admin.admin')

@section('title', 'Product Management')

@section('content')
<h2>Product Management</h2>

{{-- <div style="margin: 1rem 0; display: flex; gap: 1rem;">
    <a href="{{ route('admin.produk.create') }}" class="btn-action" id="addBtn">➕ Add Product</a>
    <button class="btn-action" id="editToggle">✏️ Edit Product</button>
</div>

<div style="margin-bottom: 1rem; display: flex; gap: 0.5rem; align-items: center;">
    <input type="text" id="searchInput" placeholder="🔍 Search Products" class="input-search" style="width: 100%;">
</div> --}}

<div class="table-container">
    <div class="table-scroll-x">
        <table class="table-products">
            <thead>
                <tr>
                    <th class="col-id">ID</th>
                    <th class="col-kode">Kode</th>
                    <th class="col-nama">Name</th>
                    <th class="col-status">Status</th>
                    <th class="col-harga">Price</th>
                    <th class="col-kategori">Category</th>
                    <th class="col-berat">Weight</th>
                    <th class="col-garansi">Warranty</th>
                    <th class="col-penitip">Item Owner</th>
                    <th class="col-deskripsi">Description</th>
                    <th class="col-action sticky-action header-action action-cell" style="display: none; background-color: #ffce53;">Edit</th>
                </tr>
            </thead>
            <tbody id="produkTableBody">
                @foreach($barangs as $barang)
                <tr>
                    <td class="center">{{ $barang->id_barang }}</td>
                    <td>{{ $barang->kode_barang }}</td>
                    <td>{{ $barang->nama_barang }}</td>
                    <td class="center">
                        @switch($barang->status_barang)
                            @case('Sold')
                                <span title="Sold">💰 Sold</span>
                                @break
                            @case('Available')
                                <span title="Available">🟢 Available</span>
                                @break
                            @case('Returned')
                                <span title="Returned">♻️ Returned</span>
                                @break
                            @case('Donated')
                                <span title="Donated">🎁 Donated</span>
                                @break
                            @case('Reserved')
                                <span title="Reserved">🎁 Donated</span>
                                @break
                            @default
                                <span>❓ {{ $barang->status_barang }}</span>
                        @endswitch
                    </td>
                    <td class="center">Rp {{ number_format($barang->harga_barang, 0, ',', '.') }}</td>
                    <td>{{ $barang->kategori->nama_kategori ?? '-' }}</td>
                    <td class="center">{{ $barang->berat_barang }} kg</td>
                    <td class="center">{{ $barang->status_garansi === 'garansi' ? 'Valid' : 'No' }}</td>
                    <td>{{ $barang->transaksiPenitipan->penitip->nama_penitip ?? '-' }}</td>
                    <td>{{ $barang->deskripsi_barang }}</td>
                    <td class="action-cell" style="background-color:rgb(255, 245, 220)">
                        <a href="{{ route('admin.produk.edit', $barang->id_barang) }}" class="edit-btn">✏️</a>
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
    const isActive = toggleBtn?.classList.contains('active');

    if (isActive) {
        headerAction?.style && (headerAction.style.display = 'table-cell');
        actionCells.forEach(cell => cell.classList.add('visible'));
    } else {
        headerAction?.style && (headerAction.style.display = 'none');
        actionCells.forEach(cell => cell.classList.remove('visible'));
    }
}

const toggleButton = document.getElementById('editToggle');
const addButton = document.getElementById('addBtn');

toggleButton.addEventListener('click', function () {
    toggleButton.classList.toggle('active');
    addButton.classList.remove('active');
    rebindEditToggle();
});

const searchInput = document.getElementById('searchInput');
let timeout = null;

searchInput.addEventListener('input', function () {
    clearTimeout(timeout);
    const query = this.value;

    timeout = setTimeout(() => {
        fetch(`{{ route('admin.produk.search') }}?q=${encodeURIComponent(query)}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.text())
        .then(html => {
            const tbody = document.getElementById('produkTableBody');
            if (tbody) {
                tbody.innerHTML = html;
                rebindEditToggle();
            }
        })
        .catch(err => console.error('Live search error:', err));
    }, 300);
});

document.querySelectorAll('.form-nonaktif, .form-reactivate').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const isDeactivate = form.classList.contains('form-nonaktif');
        Swal.fire({
            title: isDeactivate ? 'Tandai produk sebagai tidak tersedia?' : 'Tandai produk sebagai tersedia kembali?',
            text: isDeactivate ? 'Produk tidak akan tampil di etalase pembeli.' : 'Produk akan aktif kembali.',
            icon: isDeactivate ? 'warning' : 'question',
            showCancelButton: true,
            confirmButtonColor: isDeactivate ? '#d33' : '#28a745',
            cancelButtonColor: '#aaa',
            confirmButtonText: isDeactivate ? 'Ya, tandai tidak tersedia!' : 'Ya, aktifkan!'
        }).then(result => {
            if (result.isConfirmed) form.submit();
        });
    });
});
</script>

<style>
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
.table-products {
    width: max-content;
    min-width: 100%;
    border-collapse: collapse;
    background-color: #F9FAFB;
}
.table-products th,
.table-products td {
    padding: 12px;
    height: 50px;
    border: 1px solid black;
    white-space: nowrap;
}
.table-products th {
    background-color: #A7F3D0;
    text-align: center;
}
.center { text-align: center; }
.col-id       { width: 40px; }
.col-kode     { width: 80px; }
.col-nama     { width: 200px; }
.col-status   { width: 100px; }
.col-harga    { width: 140px; text-align: center; }
.col-kategori { width: 160px; }
.col-berat    { width: 100px; text-align: center; }
.col-garansi  { width: 100px; text-align: center; }
.col-penitip  { width: 160px; }
.col-deskripsi{ width: 300px; }
.col-action {
    width: 100px;
    position: sticky;
    right: 0;
    background: #fff;
    z-index: 1;
}
.sticky-action {
    position: sticky;
    right: 0;
}
.action-cell {
    position: sticky;
    right: 0;
    background: #fff;
    display: none;
    justify-content: center;
    gap: 8px;
}
.action-cell.visible {
    display: flex;
    border: none;
}
.edit-btn, .redeactivate-btn {
    border: none;
    background: none;
    cursor: pointer;
    font-size: 18px;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
}
.edit-btn {
    background-color: #FFC107;
}
.redeactivate-btn {
    background-color: #3B82F6;
}
.btn-action {
    padding: 10px 20px;
    border: 1px solid #ccc;
    border-radius: 6px;
    background: white;
    cursor: pointer;
}
.btn-action.active {
    background-color: #DCDCDC;
}
.btn-action:hover {
    background-color: #EAEAEA;
}
a.btn-action, button.btn-action {
    text-decoration: none;
    display: inline-block;
    text-align: center;
    color: black;
    font-family: inherit;
    font-size: 16px;
    font-weight: 400;
}
</style>
@endsection
