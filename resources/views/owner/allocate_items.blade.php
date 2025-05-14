@extends('owner.owner_layout')

@section('title', 'Alokasi Barang')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">Alokasi Barang</h2>
            <p class="text-muted">Alokasikan barang untuk donasi dengan mudah</p>
        </div>
    </div>

    <!-- Session Messages -->
    @if (session('success') || session('error'))
        <div id="message" class="mb-4 p-3 rounded {{ session('success') ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }}">
            {{ session('success') ?? session('error') }}
        </div>
    @else
        <div id="message" class="mb-4 p-3 rounded hidden"></div>
    @endif

    <!-- Form Card -->
    <div class="card">
        <form id="allocation-form" action="{{ route('owner.store.allocation') }}" method="POST">
            @csrf
            <div class="row g-4">
                <div class="col-md-6">
                    <label for="organisasi-select" class="form-label text-muted fw-medium">Pilih Organisasi</label>
                    <select name="id_organisasi" id="organisasi-select" class="form-select rounded-lg" required>
                        <option value="">Pilih Organisasi</option>
                        @foreach ($organisasi as $org)
                            <option value="{{ $org->id_organisasi }}" {{ $selectedOrganisasi == $org->id_organisasi ? 'selected' : '' }}>
                                {{ $org->nama_organisasi }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="request-select" class="form-label text-muted fw-medium">Pilih Request</label>
                    <select name="id_request" id="request-select" class="form-select rounded-lg" required>
                        <option value="">Pilih Request</option>
                        @if ($requests->isNotEmpty())
                            @foreach ($requests as $request)
                                <option value="{{ $request->id_request }}" {{ $requestId && $request->id_request == $requestId ? 'selected' : '' }}>
                                    {{ $request->deskripsi_request }} ({{ $request->organisasi->nama_organisasi ?? 'N/A' }})
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="barang-select" class="form-label text-muted fw-medium">Pilih Barang</label>
                    <select name="id_barang" id="barang-select" class="form-select rounded-lg" required>
                        <option value="">Pilih Barang</option>
                        @foreach ($items as $item)
                            <option value="{{ $item->id_barang }}">{{ $item->nama_barang }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="nama_penerima" class="form-label text-muted fw-medium">Nama Penerima</label>
                    <input type="text" name="nama_penerima" id="nama_penerima" class="form-control rounded-lg" required>
                </div>
                <div class="col-md-6">
                    <label for="tanggal_donasi" class="form-label text-muted fw-medium">Tanggal Donasi</label>
                    <input type="date" name="tanggal_donasi" id="tanggal_donasi" class="form-control rounded-lg" required>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-success px-4 py-2">
                    <i class="bi bi-check-circle me-2"></i> Alokasikan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
    .form-select, .form-control {
        border-color: var(--border-color);
        transition: all 0.3s;
    }

    .form-select:focus, .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(0, 177, 79, 0.25);
    }

    .form-label {
        font-size: 0.9rem;
    }

    .btn-success {
        border-radius: 8px;
    }

    .card {
        transition: transform 0.2s;
    }

    .card:hover {
        transform: translateY(-4px);
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        function showMessage(text, type) {
            const messageDiv = document.getElementById('message');
            messageDiv.innerHTML = text;
            messageDiv.className = `mb-4 p-3 rounded ${type === 'success' ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger'}`;
            messageDiv.classList.remove('hidden');
            setTimeout(() => messageDiv.classList.add('hidden'), 3000);
        }

        function updateRequests() {
            const organisasiSelect = document.getElementById('organisasi-select');
            const requestSelect = document.getElementById('request-select');
            const idOrganisasi = organisasiSelect.value;

            // Reset dropdown request
            requestSelect.innerHTML = '<option value="">Pilih Request</option>';

            if (!idOrganisasi) {
                return;
            }

            // AJAX call ke server
            fetch(`{{ route('owner.requests.by_organisasi') }}?id_organisasi=${idOrganisasi}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                data.forEach(req => {
                    const option = document.createElement('option');
                    option.value = req.id_request;
                    option.textContent = req.deskripsi_request;
                    requestSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error fetching requests:', error);
                showMessage('Gagal memuat request donasi.', 'error');
            });
        }

        // Event listener untuk ganti organisasi
        document.getElementById('organisasi-select').addEventListener('change', updateRequests);

        // Panggil updateRequests saat halaman dimuat kalau ada requestId
        const urlParams = new URLSearchParams(window.location.search);
        const requestId = urlParams.get('id_request');
        if (requestId) {
            updateRequests();
        }

        document.getElementById('allocation-form').addEventListener('submit', (e) => {
            if (!e.target.checkValidity()) {
                e.preventDefault();
                showMessage('Harap isi semua kolom dengan benar.', 'error');
            }
        });
    });
</script>
@endpush