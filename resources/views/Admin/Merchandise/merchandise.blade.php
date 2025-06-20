@extends('Admin.admin')

@section('title', 'Merchandise Management')

@section('content')
<h2>Merchandise Management</h2>

{{-- <div style="margin: 1rem 0; display: flex; gap: 1rem;">
    <a href="{{ route('admin.merchandise.create') }}" class="btn-action" id="addBtn">➕ Add Merchandise</a>
    <button class="btn-action" id="editToggle">✏️ Edit Merchandise</button>
</div>

<div style="margin-bottom: 1rem; display: flex; gap: 0.5rem; align-items: center;">
    <input type="text" id="searchInput" placeholder="🔍 Search Merchandise" class="input-search" style="width: 100%;">
</div> --}}

<div class="table-container">
    <div class="table-scroll-x">
        <table class="table-merchandise">
            <thead>
                <tr>
                    <th class="col-id">ID</th>
                    <th class="col-nama">Name</th>
                    <th class="col-poin">Poin</th>
                    <th class="col-stok">Stock</th>
                    <th class="col-status">Status</th>
                    <th class="col-added">Added By</th>
                    <th class="col-modified">Last Modified By</th>
                    <th class="col-action sticky-action header-action action-cell" style="display: none; background-color: #ffce53; border: none;">Edit</th>
                </tr>
            </thead>
            <tbody id="merchTableBody">
                @foreach($merches as $merch)
                <tr>
                    <td class="center">{{ $merch->id_merchandise }}</td>
                    <td>{{ $merch->nama_merch }}</td>
                    <td class="center">{{ $merch->poin }}</td>
                    <td class="center">{{ $merch->stok }}</td>
                    <td class="center">
                        @if($merch->stok > 0)
                            <span style="color: green; font-weight: bold;">🟢 Available</span>
                        @else
                            <span style="color: #E53E3E; font-weight: bold;">🔴 Out of Stock</span>
                        @endif
                    </td>
                    <td>{{ $merch->addedBy->nama_pegawai ?? '-' }}</td>
                    <td>{{ $merch->modifiedBy->nama_pegawai ?? '-' }}</td>
                    <td class="action-cell" style="background-color:rgb(255, 245, 220); border: none;">
                        <a href="{{ route('admin.merchandise.edit', $merch->id_merchandise) }}" class="edit-btn">✏️</a>
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
        fetch(`{{ route('admin.merchandise.search') }}?q=${encodeURIComponent(query)}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.text())
        .then(html => {
            const tbody = document.getElementById('merchTableBody');
            if (tbody) {
                tbody.innerHTML = html;
                rebindEditToggle();
            }
        })
        .catch(err => console.error('Live search error:', err));
    }, 300);
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
.table-merchandise {
    width: max-content;
    min-width: 100%;
    border-collapse: collapse;
    background-color: #F9FAFB;
}
.table-merchandise th,
.table-merchandise td {
    padding: 12px;
    height: 50px;
    border: 1px solid black;
    white-space: nowrap;
}
.table-merchandise th {
    background-color: #A7F3D0;
    text-align: center;
}
.center { text-align: center; }
.col-id       { width: 40px; }
.col-nama     { width: 200px; }
.col-poin     { width: 100px; text-align: center; }
.col-stok     { width: 100px; text-align: center; }
.col-status   { width: 140px; text-align: center; }
.col-added    { width: 160px; }
.col-modified { width: 160px; }
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
    border:none;
}
.action-cell.visible {
    display: flex;
}
.edit-btn {
    background-color: #FFC107;
    cursor: pointer;
    font-size: 18px;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    border: none;
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
