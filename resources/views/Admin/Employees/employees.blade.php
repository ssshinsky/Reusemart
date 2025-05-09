@extends('Admin.admin')

@section('title', 'Employee Management')

@section('content')
<h2>Employee Management</h2>

<div style="margin: 1rem 0; display: flex; gap: 1rem;">
    <a href="{{ route('admin.employees.create') }}" class="btn-action" id="addBtn">
        ➕ Add Employee
    </a>
    <button class="btn-action" id="editToggle">✏️ Edit Employee</button>
</div>

<div style="margin-bottom: 1rem; display: flex; gap: 0.5rem; align-items: center;">
    <input type="text" id="searchInput" placeholder="🔍 Search Employees" class="input-search" style="width: 100%;">
</div>

<div class="table-container">
    <div class="table-scroll-x">
        <table class="table-employees">
            <thead>
                <tr>
                    <th class="col-id">ID</th>
                    <th class="col-role">Role</th>
                    <th class="col-nama">Nama</th>
                    <th class="col-email">Email</th>
                    <th class="col-gaji">Gaji</th>
                    <th class="col-tanggal">Tanggal Lahir</th>
                    <th class="col-telp">Nomor Telepon</th>
                    <th class="col-alamat">Alamat</th>
                    <th class="col-action sticky-action header-action action-cell" style="display: none; background-color: #ffce53;">Edit</th>
                </tr>
            </thead>
            <tbody id="pegawaiTableBody">
                @foreach($pegawais as $pegawai)
                    <tr>
                        <td class="center">{{ $pegawai->id_pegawai }}</td>
                        <td>{{ $pegawai->id_role == 3 ? 'CS' : ($pegawai->role->nama_role ?? '-') }}</td>
                        <td style="{{ !$pegawai->is_active ? 'color: #E53E3E; font-weight: bold;' : '' }}">
                            {{ $pegawai->nama_pegawai }}
                        </td>
                        <td>{{ $pegawai->email_pegawai }}</td>
                        <td class="nowrap center">Rp {{ number_format($pegawai->gaji_pegawai, 0, ',', '.') }}</td>
                        <td class="nowrap center">{{ \Carbon\Carbon::parse($pegawai->tanggal_lahir)->format('d-m-Y') }}</td>
                        <td class="center">{{ $pegawai->nomor_telepon }}</td>
                        <td>{{ $pegawai->alamat_pegawai }}</td>
                        <td class="action-cell" style="background-color:rgb(255, 245, 220)">
                            <a href="{{ route('admin.employees.edit', $pegawai->id_pegawai) }}" class="edit-btn">✏️</a>

                            @if($pegawai->is_active)
                                <form action="{{ route('admin.employees.deactivate', $pegawai->id_pegawai) }}" method="POST" class="form-nonaktif" style="display:inline;">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="redeactivate-btn" title="Deactivate Pegawai">🛑</button>
                                </form>
                            @else
                                <form action="{{ route('admin.employees.reactivate', $pegawai->id_pegawai) }}" method="POST" class="form-reactivate" style="display:inline;">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="redeactivate-btn" title="Reactivate Pegawai">♻️</button>
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

    const searchInput = document.getElementById('searchInput');

    let timeout = null;
    searchInput.addEventListener('input', function () {
        clearTimeout(timeout);
        const query = this.value;

        timeout = setTimeout(() => {
            fetch(`{{ route('admin.employees.search') }}?q=${encodeURIComponent(query)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.text())
            .then(html => {
                const tbody = document.getElementById('pegawaiTableBody');
                if (tbody) {
                    tbody.innerHTML = html;
                    rebindEditToggle(); // Reapply toggle logic to new content
                }
            })
            .catch(err => {
                console.error('Live search error:', err);
            });
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

<script>
    const toggleButton = document.getElementById('editToggle');
    const addButton = document.getElementById('addBtn');
    const actionCells = document.querySelectorAll('.action-cell');
    const headerAction = document.querySelector('.header-action');

    toggleButton.addEventListener('click', function () {
        const isVisible = toggleButton.classList.toggle('active');
        rebindEditToggle(); // penting
        addButton.classList.remove('active');
    });
</script>

<style>
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

    input.input-search{
        text-decoration: none;
        display: inline-block;
        color: black;
        font-family: inherit;
        font-size: 16px;
        font-weight: 400;
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

    .input-search {
        flex: 1;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 6px;
    }

    .table-container {
        max-height: 500px;
        overflow-y: auto;
        margin-top: 20px;
        border: 1px solid #ccc;
        border-radius: 6px;
    }

    .table-scroll-x {
        overflow-x: auto;
    }

    .table-employees {
        width: max-content;
        min-width: 100%;
        border-collapse: collapse;
        background-color: #F9FAFB;
    }

    .table-employees th, .table-employees td {
        padding: 12px;
        height: 50px;
        border: 1px solid black;
        white-space: nowrap;
    }

    .table-employees th {
        background-color: #A7F3D0;
        text-align: center;
    }

    .nowrap { white-space: nowrap; }
    .center { text-align: center; }

    .col-id      { width: 40px; }
    .col-role    { width: 80px; }
    .col-nama    { width: 180px; }
    .col-alamat  { width: 400px; }
    .col-tanggal { width: 120px; }
    .col-telp    { width: 140px; }
    .col-gaji    { width: 140px; }
    .col-email   { width: 240px; }
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
        background-color: #3B82F6 ;
    }
</style>

@endsection
