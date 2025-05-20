@extends('owner.owner_layout')

@section('title', 'Update Donasi')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">Update Donasi</h2>
            <p class="text-muted">Perbarui informasi donasi dan status barang</p>
        </div>
    </div>

    <!-- Session Messages -->
    <div id="message" class="mb-4 p-3 rounded hidden">
        @if (session('success'))
            <div class="bg-success bg-opacity-10 text-success">{{ session('success') }}</div>
        @elseif (session('error'))
            <div class="bg-danger bg-opacity-10 text-danger">{{ session('error') }}</div>
        @endif
    </div>

    <!-- Form Card -->
    <div class="card">
        <form id="update-donation-form" action="{{ route('owner.update.donasi.store') }}" method="POST">
            @csrf
            <div class="row g-4 p-4">
                <div class="col-md-6">
                    <label for="donasi-select" class="form-label text-muted fw-medium">Pilih Donasi</label>
                    <select name="id_donasi" id="donasi-select" class="form-select rounded-lg" required>
                        <option value="">Pilih Donasi</option>
                        @foreach ($donations as $donasi)
                            <option value="{{ $donasi->id_donasi }}" {{ $selectedDonasi && $selectedDonasi->id_donasi == $donasi->id_donasi ? 'selected' : '' }}>
                                {{ $donasi->barang->nama_barang ?? 'N/A' }} ({{ $donasi->nama_penerima }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="tanggal_donasi" class="form-label text-muted fw-medium">Tanggal Donasi</label>
                    <input type="date" name="tanggal_donasi" id="tanggal_donasi" class="form-control rounded-lg" value="{{ old('tanggal_donasi', $selectedDonasi ? $selectedDonasi->tanggal_donasi : '') }}" required>
                </div>
                <div class="col-md-6">
                    <label for="nama_penerima" class="form-label text-muted fw-medium">Nama Penerima</label>
                    <input type="text" name="nama_penerima" id="nama_penerima" class="form-control rounded-lg" value="{{ old('nama_penerima', $selectedDonasi ? $selectedDonasi->nama_penerima : '') }}" required>
                </div>
                <div class="col-md-6">
                    <label for="status_barang" class="form-label text-muted fw-medium">Status Barang</label>
                    <select name="status_barang" id="status_barang" class="form-select rounded-lg" required>
                        <option value="barang untuk donasi" {{ old('status_barang', $selectedDonasi ? $selectedDonasi->barang->status_barang : '') === 'barang untuk donasi' ? 'selected' : '' }}>Barang Untuk Donasi</option>
                        <option value="didonasikan" {{ old('status_barang', $selectedDonasi ? $selectedDonasi->barang->status_barang : '') === 'didonasikan' ? 'selected' : '' }}>Didonasikan</option>
                    </select>
                </div>
            </div>
            <div class="p-4">
                <button type="submit" class="btn btn-success px-4 py-2">
                    <i class="bi bi-check-circle me-2"></i> Update & Notif Penitip
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
        document.getElementById('update-donation-form').addEventListener('submit', (e) => {
            if (!e.target.checkValidity()) {
                e.preventDefault();
                showMessage('Harap isi semua kolom dengan benar.', 'error');
            }
        });

        function showMessage(text, type) {
            const messageDiv = document.getElementById('message');
            messageDiv.textContent = text;
            messageDiv.className = `mb-4 p-3 rounded ${type === 'success' ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger'}`;
            messageDiv.classList.remove('hidden');
            setTimeout(() => messageDiv.classList.add('hidden'), 3000);
        }

        document.getElementById('donasi-select').addEventListener('change', function () {
            const idDonasi = this.value;
            if (idDonasi) {
                fetch(`{{ route('owner.get.donasi') }}?id_donasi=${idDonasi}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('tanggal_donasi').value = data.tanggal_donasi;
                    document.getElementById('nama_penerima').value = data.nama_penerima;
                    document.getElementById('status_barang').value = data.barang.status_barang;
                })
                .catch(error => {
                    console.error('Error fetching donasi:', error);
                    showMessage('Gagal memuat data donasi.', 'error');
                });
            }
        });
    });
</script>
@endpush