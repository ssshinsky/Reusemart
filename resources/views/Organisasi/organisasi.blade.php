@extends('Organisasi.dashboard')

@section('title', 'Request Donasi')

@section('content')
<h2>Request Donasi</h2>

<div style="margin: 1rem 0; display: flex; gap: 1rem;">
    <a href="{{ route('organisasi.request.create') }}" class="btn-action" id="addBtn">➕ Add Request</a>
    <button class="btn-action" id="editToggle">✏️ Edit Request</button>
</div>

<div style="margin-bottom: 1rem;">
    <input type="text" id="searchInput" placeholder="🔍 Search Requests" class="input-search" style="width: 100%;">
</div>

<div class="table-container">
    <div class="table-scroll-x">
        <table class="table-org">
            <thead>
                <tr>
                    <th class="col-id">No</th>
                    <th class="col-request">Request</th>
                    <th class="col-status">Status</th>
                    <th class="col-action sticky-action header-action action-cell" style="display: none; background-color: #ffce53;">Edit</th>
                </tr>
            </thead>
            <tbody id="requestTableBody">
                @foreach($requests as $key => $request)
                <tr>
                    <td class="center">{{ $key + 1 }}</td>
                    <td>{{ $request->request }}</td>
                    <td class="center">{{ $request->status_request }}</td>
                    <td class="action-cell" style="background-color:rgb(255, 245, 220)">
                        <a href="{{ route('organisasi.request.edit', $request->id_request) }}" class="edit-btn">✏️</a>
                        <form action="{{ route('organisasi.request.destroy', $request->id_request) }}" method="POST" class="form-nonaktif" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="delete-request-btn" title="Delete">🗑️</button>
                        </form>
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
            fetch(`{{ route('organisasi.request.search') }}?q=${encodeURIComponent(query)}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.text())
            .then(html => {
                const tbody = document.getElementById('requestTableBody');
                if (tbody) {
                    tbody.innerHTML = html;
                    rebindEditToggle();
                    bindDeleteRequestButtons();
                }
            })
            .catch(err => console.error('Live search error:', err));
        }, 300);
    });

    function bindDeleteRequestButtons() {
        document.querySelectorAll('.delete-request-btn').forEach(button => {
            button.closest('form')?.addEventListener('submit', function (e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Delete this request?',
                    text: 'This request will be permanently removed.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#aaa',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });
    }
    document.addEventListener('DOMContentLoaded', function () {
        bindDeleteRequestButtons();
        rebindEditToggle();
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
        background: #fff;
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
        text-align: center;
    }

    .table-org th {
        background-color: #A7F3D0;
        text-align: center;
    }

    .center { text-align: center; }
    .col-id      { width: 5%; }
    .col-request    { width: 75%; }
    .col-status  { width: 20%; text-align: center; }
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

    .edit-btn, .delete-request-btn {
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

    .delete-request-btn {
        background-color: #FF3B3F;
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
