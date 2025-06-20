@extends('Admin.admin')

@section('title', 'Customer Management')

@section('content')
<h2>Customer Management</h2>

<div style="margin-bottom: 1rem; display: flex; gap: 0.5rem; align-items: center;">
    <input type="text" id="searchInput" placeholder="🔍 Search Customers" class="input-search" style="width: 100%;">
</div>

<div class="table-container">
    <div class="table-scroll-x">
        <table class="table-customers">
            <thead>
                <tr>
                    <th class="col-id">ID</th>
                    <th class="col-nama">Nama</th>
                    <th class="col-status">Status</th>
                    <th class="col-email">Email</th>
                    <th class="col-telp">Phone Number</th>
                    <th class="col-poin">Point</th>
                    <th class="col-tanggal">Birth Date</th>
                    <th class="col-alamat">Default Address</th>
                </tr>
            </thead>
            <tbody id="pembeliTableBody">
                @foreach($pembelis as $pembeli)
                <tr>
                    <td class="center">{{ $pembeli->id_pembeli }}</td>
                    <td>{{ $pembeli->nama_pembeli }}</td>
                    <td>{{ $pembeli->status_pembeli }}</td>
                    <td>{{ $pembeli->email_pembeli }}</td>
                    <td>{{ $pembeli->nomor_telepon }}</td>
                    <td class="center">{{ $pembeli->poin_pembeli }}</td>
                    <td class="center">{{ \Carbon\Carbon::parse($pembeli->tanggal_lahir)->format('Y-m-d') }}</td>
                    <td>{{ $pembeli->alamatDefault->alamat_lengkap ?? '-' }}</td>
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
        fetch(`{{ route('admin.pembeli.search') }}?q=${encodeURIComponent(query)}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.text())
        .then(html => {
            const tbody = document.getElementById('pembeliTableBody');
            if (tbody) {
                tbody.innerHTML = html;
                rebindEditToggle();
            }
        })
        .catch(err => console.error('Live search error:', err));
    }, 300);
});

document.querySelectorAll('.form-delete').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Hapus pembeli?',
            text: 'Data pembeli akan dihapus permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#aaa',
            confirmButtonText: 'Ya, hapus!'
        }).then(result => { if (result.isConfirmed) form.submit(); });
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
.table-customers {
    width: max-content;
    min-width: 100%;
    border-collapse: collapse;
    background-color: #F9FAFB;
}
.table-customers th,
.table-customers td {
    padding: 12px;
    height: 50px;
    border: 1px solid black;
    white-space: nowrap;
}
.table-customers th {
    background-color: #A7F3D0;
    text-align: center;
}
.center { text-align: center; }
.col-id      { width: 40px; }
.col-nama    { width: 160px; }
.col-email   { width: 220px; }
.col-tanggal { width: 120px; }
.col-telp    { width: 140px; }
.col-poin    { width: 100px; }
.col-action  {
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
    background-color: #E53E3E;
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
