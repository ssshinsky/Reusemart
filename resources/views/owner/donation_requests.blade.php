@extends('owner.owner_layout')

@section('title', 'Daftar Request Donasi')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">Daftar Request Donasi</h2>
            <p class="text-muted">Kelola semua permintaan donasi dari organisasi</p>
        </div>
    </div>

    <!-- Controls -->
    <div class="d-flex flex-wrap gap-3 mb-4 align-items-center">
        <div class="flex-grow-1">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" id="searchInput" placeholder="Cari Request" class="form-control border-start-0" style="border-radius: 0 8px 8px 0;">
            </div>
        </div>
        <button class="btn btn-outline-primary" id="actionToggle">
            <i class="bi bi-pencil-square me-2"></i> Aksi
        </button>
        <a href="{{ route('owner.download.pdf') }}" class="btn btn-outline-success" target="_blank">
            <i class="bi bi-download me-2"></i> Unduh PDF
        </a>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th scope="col" style="width: 80px;">ID Request</th>
                        <th scope="col" style="width: 200px;">Organisasi</th>
                        <!-- <th scope="col" style="width: 200px;">Pegawai</th> -->
                        <th scope="col" style="width: 400px;">Request</th>
                        <th scope="col" class="header-action" style="display: none; width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="requestTableBody">
                    @forelse ($requests ?? [] as $request)
                        <tr>
                            <td class="text-center">{{ $request->id_request }}</td>
                            <td>{{ $request->organisasi->nama_organisasi ?? 'N/A' }}</td>
                            <!-- <td>{{ $request->pegawai->nama_pegawai ?? 'N/A' }}</td> -->
                            <td>{{ $request->request }}</td>
                            <td class="action-cell">
                                <div class="d-flex gap-2 justify-content-center">
                                    <button class="btn btn-sm btn-outline-primary action-btn" onclick="processRequest({{ $request->id_request }})" title="Proses">
                                        <i class="bi bi-box-seam"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger action-btn" onclick="deleteRequest({{ $request->id_request }})" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="bi bi-info-circle me-2"></i> Belum ada request donasi
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table th {
        background-color: var(--bg-light);
        border-bottom: 2px solid var(--border-color);
        font-weight: 600;
    }

    .table td {
        border-bottom: 1px solid var(--border-color);
    }

    .action-cell {
        background-color: #fff;
        display: none;
        border: none !important;
    }

    .action-cell.visible {
        display: table-cell;
    }

    .header-action {
        background-color: var(--bg-light) !important;
    }

    .action-btn {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        transition: all 0.3s;
    }

    .btn-outline-primary.action-btn:hover {
        background-color: var(--primary-color);
        color: white;
    }

    .btn-outline-danger.action-btn:hover {
        background-color: #dc3545;
        color: white;
    }

    .input-group-text {
        border-color: var(--border-color);
    }

    .form-control {
        border-color: var(--border-color);
        border-radius: 8px;
    }

    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(0, 177, 79, 0.25);
    }

    .btn-outline-primary {
        border-color: var(--primary-color);
        color: var(--primary-color);
        border-radius: 8px;
    }

    .btn-outline-primary:hover {
        background-color: var(--primary-color);
        color: white;
    }
</style>
@endpush

@push('scripts')
<script>
    function rebindActionToggle() {
        const actionCells = document.querySelectorAll('.action-cell');
        const headerAction = document.querySelector('.header-action');
        const toggleBtn = document.getElementById('actionToggle');
        const isActive = toggleBtn?.classList.contains('active');

        if (isActive) {
            headerAction.style.display = 'table-cell';
            actionCells.forEach(cell => cell.classList.add('visible'));
        } else {
            headerAction.style.display = 'none';
            actionCells.forEach(cell => cell.classList.remove('visible'));
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('searchInput');
        let timeout = null;

        // Filter data di sisi client berdasarkan input pencarian
        function filterRequests(query = '') {
            const rows = document.querySelectorAll('#requestTableBody tr');
            rows.forEach(row => {
                const org = row.cells[1].textContent.toLowerCase();
                const pegawai = row.cells[2].textContent.toLowerCase();
                const requestText = row.cells[3].textContent.toLowerCase();
                if (query === '' || org.includes(query) || pegawai.includes(query) || requestText.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        searchInput.addEventListener('input', function () {
            clearTimeout(timeout);
            const query = this.value.toLowerCase();
            timeout = setTimeout(() => filterRequests(query), 300);
        });

        const toggleButton = document.getElementById('actionToggle');
        toggleButton.addEventListener('click', () => {
            toggleButton.classList.toggle('active');
            rebindActionToggle();
        });

        window.processRequest = (id) => {
            window.location.href = '{{ route("owner.allocate.items") }}?request_id=' + id;
        };

        window.deleteRequest = (id) => {
            Swal.fire({
                title: 'Yakin menghapus request ini?',
                text: 'Request ini akan dihapus permanen!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#aaa',
                confirmButtonText: 'Ya, hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`{{ route('owner.delete.request', ['id' => '__id__']) }}`.replace('__id__', id), {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        Swal.fire('Berhasil!', data.message, 'success');
                        location.reload();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Gagal!', 'Gagal menghapus request', 'error');
                    });
                }
            });
        };
    });
</script>
@endpush