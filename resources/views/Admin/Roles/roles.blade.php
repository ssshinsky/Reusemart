@extends('Admin.admin')

@section('title', 'Role Management')

@section('content')
<h2>Role Management</h2>

<div style="margin: 1rem 0; display: flex; gap: 1rem;">
    <a href="{{ route('admin.roles.create') }}" class="btn-action" id="addBtn">➕ Add Role</a>
    <button class="btn-action" id="editToggle">✏️ Edit Role</button>
</div>

<div style="margin-bottom: 1rem; display: flex; gap: 0.5rem; align-items: center;">
    <input type="text" id="searchInput" placeholder="🔍 Search Roles" class="input-search" style="width: 100%;">
</div>

<div class="table-container">
    <div class="table-scroll-x">
        <table class="table-roles">
            <thead>
                <tr>
                    <th class="col-id">ID</th>
                    <th class="col-nama">Nama Role</th>
                    <th class="col-action sticky-action header-action action-cell" style="display: none; background-color: #ffce53; text-align: center">Edit</th>
                </tr>
            </thead>
            <tbody>
                @foreach($roles as $role)
                <tr>
                    <td class="center">{{ $role->id_role }}</td>
                    <td style="{{ !$role->is_active ? 'color: #E53E3E; font-weight: bold;' : '' }}">{{ $role->nama_role }}</td>
                    <td class="action-cell" style="background-color:rgb(255, 245, 220)">
                        <a href="{{ route('admin.roles.edit', $role->id_role) }}" class="edit-btn">✏️</a>
                        @if($role->is_active)
                        <form action="{{ route('admin.roles.deactivate', $role->id_role) }}" method="POST" class="form-nonaktif" style="display:inline;">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="redeactivate-btn" title="Deactivate Role">🛑</button>
                        </form>
                        @else
                        <form action="{{ route('admin.roles.reactivate', $role->id_role) }}" method="POST" class="form-reactivate" style="display:inline;">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="redeactivate-btn" title="Reactivate Role">♻️</button>
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
    const actionCells = document.querySelectorAll('.action-cell');
    const headerAction = document.querySelector('.header-action');

    toggleButton.addEventListener('click', function () {
        this.classList.toggle('active');
        rebindEditToggle(); // <== ini wajib dipanggil ulang
        addButton.classList.remove('active');
    });


    document.querySelectorAll('.form-nonaktif').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: 'This role will be deactivated!',
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

    const searchInput = document.getElementById('searchInput');
    let timeout = null;

    searchInput.addEventListener('input', function () {
        clearTimeout(timeout);
        const query = this.value;

        timeout = setTimeout(() => {
            fetch(`{{ route('admin.roles.search') }}?q=${encodeURIComponent(query)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.text())
            .then(html => {
                const tbody = document.querySelector('.table-roles
 tbody');
                if (tbody) {
                    tbody.innerHTML = html;
                    rebindEditToggle(); // penting agar tombol edit tetap aktif
                }
            })
            .catch(err => console.error('Live search error:', err));
        }, 300);
    });

    document.querySelectorAll('.form-reactivate').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Reactivate this role?',
                text: 'This role will regain access.',
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
    }

    input.input-search{
        text-decoration: none;
        display: inline-block;
        color: black;
        font-family: inherit;
        font-size: 16px;
        font-weight: 400;
    }
    
    .table-container {
        margin-top: 1rem;
        width: 100%;
        border: 1px solid #ccc;
        border-radius: 6px;
        overflow-x: auto;
        max-height: 500px
    }

    .table-roles
 {
        width: auto;
        border-collapse: collapse;
        background-color: #F9FAFB;
    }

    .table-scroll-x {
        overflow-x: auto;
    }

    .table-roles
 th,
    .table-roles
 td {
        padding: 10px 16px;
        height: 50px;
        border: 1px solid black;
        white-space: nowrap;
        text-align: center;
    }

    .table-roles
 th {
        background-color: #A7F3D0;
        text-align: center;
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

    a.btn-action,
    button.btn-action {
        text-decoration: none;
        display: inline-block;
        text-align: center;
        color: black;
        font-family: inherit;
        font-size: 16px;
        font-weight: 400;
    }

    .col-id { width: 10%; text-align: center; }
    .col-nama { width: 70%; }
    .col-action { width: 20%; text-align: center; }
</style>
@endsection