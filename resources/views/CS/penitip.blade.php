@extends('CS.dashboard')

@section('title', 'Item Owners Management')

@section('content')
<h2>Item Owners Management</h2>

<div style="margin: 1rem 0; display: flex; gap: 1rem;">
    <a href="{{ route('cs.penitip.create') }}" class="btn-action" id="addBtn">➕ Add Item Owner</a>
    <button class="btn-action" id="editToggle">✏️ Edit Item Owner</button>
</div>

<div style="margin-bottom: 1rem; display: flex; gap: 0.5rem; align-items: center;">
    <input type="text" id="searchInput" placeholder="🔍 Search Item Owners" class="input-search" style="width: 100%;">
</div>

<div class="table-container">
    <div class="table-scroll-x">
        <table class="table-penitip">
            <thead>
                <tr>
                    <th class="col-id">ID</th>
                    <th class="col-nama">Nama</th>
                    <th class="col-nik">NIK</th>
                    <th class="col-email">Email</th>
                    <th class="col-telp">Nomor Telepon</th>
                    <th class="col-alamat">Alamat</th>
                    <th class="col-saldo">Saldo</th>
                    <th class="col-rating">Rating</th>
                    <th class="col-status">Status</th>
                    <th class="col-action sticky-action header-action action-cell" style="display: none; background-color: #ffce53;">Edit</th>
                </tr>
            </thead>
            <tbody id="penitipTableBody">
                @foreach($penitips as $penitip)
                <tr>
                    <td class="center">{{ $penitip->id_penitip }}</td>
                    <td style="{{ $penitip->status_penitip !== 'Active' ? 'color: #E53E3E; font-weight: bold;' : '' }}">{{ $penitip->nama_penitip }}</td>
                    <td>{{ $penitip->nik_penitip }}</td>
                    <td>{{ $penitip->email_penitip }}</td>
                    <td>{{ $penitip->no_telp }}</td>
                    <td>{{ $penitip->alamat }}</td>
                    <td class="center">Rp {{ number_format($penitip->saldo_penitip, 0, ',', '.') }}</td>
                    <td class="center">{{ $penitip->rata_rating }}</td>
                    <td class="center">{{ $penitip->status_penitip }}</td>
                    <td class="action-cell" style="background-color:rgb(255, 245, 220)">
                        <a href="{{ route('cs.penitip.edit', $penitip->id_penitip) }}" class="edit-btn">✏️</a>
                        @if($penitip->status_penitip === 'Active')
                        <form action="{{ route('cs.penitip.deactivate', $penitip->id_penitip) }}" method="POST" class="form-nonaktif" style="display:inline;">
                            @csrf @method('PUT')
                            <button type="submit" class="redeactivate-btn">🛑</button>
                        </form>
                        @else
                        <form action="{{ route('cs.penitip.reactivate', $penitip->id_penitip) }}" method="POST" class="form-reactivate" style="display:inline;">
                            @csrf @method('PUT')
                            <button type="submit" class="redeactivate-btn">♻️</button>
                        </form>
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
            fetch(`{{ route('cs.penitip.search') }}?q=${encodeURIComponent(query)}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.text())
            .then(html => {
                const tbody = document.getElementById('penitipTableBody');
                if (tbody) {
                    tbody.innerHTML = html;
                    rebindEditToggle();
                    bindDeactivateButtons();
                    bindReactivateButtons();
                }
            })
            .catch(err => console.error('Live search error:', err));
        }, 300);
    });

    document.querySelectorAll('.form-nonaktif').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: 'This employee will be deactivated!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#aaa',
                confirmButtonText: 'Yes, deactivate!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
    
    document.querySelectorAll('.form-reactivate').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Reactivate this employee?',
                text: 'This employee will regain access to the system.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#aaa',
                confirmButtonText: 'Yes, reactivate!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    function bindDeactivateButtons() {
        document.querySelectorAll('.form-nonaktif').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'This employee will be deactivated!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#aaa',
                    confirmButtonText: 'Yes, deactivate!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    }

    function bindReactivateButtons() {
        document.querySelectorAll('.form-reactivate').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Reactivate this employee?',
                    text: 'This employee will regain access to the system.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#aaa',
                    confirmButtonText: 'Yes, reactivate!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    }

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
    border: 1px solid black;
    white-space: nowrap;
}
.table-penitip th {
    background-color: #A7F3D0;
    text-align: center;
}
.center { text-align: center; }
.col-id      { width: 40px; }
.col-nama    { width: 180px; }
.col-nik     { width: 100px;}
.col-email   { width: 220px; }
.col-telp    { width: 140px; }
.col-alamat  { width: 300px; }
.col-saldo   { width: 120px; }
.col-rating  { width: 100px; }
.col-status  { width: 100px; text-align: center; }
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
