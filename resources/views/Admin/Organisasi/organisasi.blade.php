@extends('Admin.admin')

@section('title', 'Organization Management')

@section('content')
<h2>Organization Management</h2>

<div style="margin: 1rem 0; display: flex; gap: 1rem;">
    <a href="{{ route('admin.organisasi.create') }}" class="btn-action" id="addBtn">➕ Add Organization</a>
    <button class="btn-action" id="editToggle">✏️ Edit Organization</button>
</div>

<div style="margin-bottom: 1rem; display: flex; gap: 0.5rem; align-items: center;">
    <input type="text" id="searchInput" placeholder="🔍 Search Organizations" class="input-search" style="width: 100%;">
</div>

<div class="table-container">
    <div class="table-scroll-x">
        <table class="table-org">
            <thead>
                <tr>
                    <th class="col-id">ID</th>
                    <th class="col-nama">Name</th>
                    <th class="col-email">Email</th>
                    <th class="col-kontak">Contact Person</th>
                    <th class="col-alamat">Address</th>
                    <th class="col-status">Status</th>
                    <th class="col-action sticky-action header-action action-cell" style="display: none; background-color: #ffce53;">Edit</th>
                </tr>
            </thead>
            <tbody id="organisasiTableBody">
                @foreach($organisasis as $organisasi)
                <tr>
                    <td class="center">{{ $organisasi->id_organisasi }}</td>
                    <td style="{{ $organisasi->status_organisasi !== 'Active' ? 'color: #E53E3E; font-weight: bold;' : '' }}">{{ $organisasi->nama_organisasi }}</td>
                    <td>{{ $organisasi->email_organisasi }}</td>
                    <td>{{ $organisasi->kontak }}</td>
                    <td>{{ $organisasi->alamat }}</td>
                    <td class="center">{{ $organisasi->status_organisasi }}</td>
                    <td class="action-cell" style="background-color:rgb(255, 245, 220)">
                        <a href="{{ route('admin.organisasi.edit', $organisasi->id_organisasi) }}" class="edit-btn">✏️</a>
                        @if($organisasi->status_organisasi === 'Active')
                        <form action="{{ route('admin.organisasi.deactivate', $organisasi->id_organisasi) }}" method="POST" class="form-nonaktif" style="display:inline;">
                            @csrf @method('PUT')
                            <button type="submit" class="redeactivate-btn">🛑</button>
                        </form>
                        @else
                        <form action="{{ route('admin.organisasi.reactivate', $organisasi->id_organisasi) }}" method="POST" class="form-reactivate" style="display:inline;">
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
            fetch(`{{ route('admin.organisasi.search') }}?q=${encodeURIComponent(query)}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.text())
            .then(html => {
                const tbody = document.getElementById('organisasiTableBody');
                if (tbody) {
                    tbody.innerHTML = html;
                    rebindEditToggle();
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
.table-org {
    width: max-content;
    min-width: 100%;
    border-collapse: collapse;
    background-color: #F9FAFB;
}
.table-org th,
.table-org td {
    padding: 12px;
    height: 50px;
    border: 1px solid black;
    white-space: nowrap;
}
.table-org th {
    background-color: #A7F3D0;
    text-align: center;
}
.center { text-align: center; }
.col-id     { width: 40px; }
.col-nama   { width: 200px; }
.col-email  { width: 200px; }
.col-kontak { width: 130px; }
.col-alamat { width: 250px; }
.col-status { width: 100px; }
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
.edit-btn { background-color: #FFC107; }
.redeactivate-btn { background-color: #3B82F6; }
.btn-action {
    padding: 10px 20px;
    border: 1px solid #ccc;
    border-radius: 6px;
    background: white;
    cursor: pointer;
}
.btn-action.active { background-color: #DCDCDC; }
.btn-action:hover { background-color: #EAEAEA; }
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