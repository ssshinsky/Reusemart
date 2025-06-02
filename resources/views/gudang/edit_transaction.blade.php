@extends('gudang.gudang_layout')

@section('title', 'Edit Transaksi Barang Titipan')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-2">Edit Transaksi #{{ $transaction->id_transaksi_penitipan }}</h2>
            <p class="text-muted mb-0">Ubah detail transaksi dan barang titipan</p>
        </div>
        <a href="{{ route('gudang.transaction.list') }}" class="btn btn-outline-secondary btn-sm rounded-pill">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('gudang.transaction.update', $transaction->id_transaksi_penitipan) }}" method="POST" enctype="multipart/form-data" id="updateTransactionForm">
        @csrf
        @method('PUT')

        <!-- Transaksi Info -->
        <div class="card shadow-lg border-0 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold text-primary mb-4"><i class="fas fa-info-circle me-2"></i>Informasi Transaksi</h5>
                <div class="row g-3">
                    <div class="col-md-6 info-group">
                        <label for="id_qc" class="form-label">QC:</label>
                        <select name="id_qc" id="id_qc" class="form-select @error('id_qc') is-invalid @endif" required>
                            @foreach ($qcs as $qc)
                                <option value="{{ $qc->id_pegawai }}" {{ $transaction->id_qc == $qc->id_pegawai ? 'selected' : '' }}>
                                    {{ $qc->nama_pegawai }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_qc')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @endif
                    </div>
                    <div class="col-md-6 info-group">
                        <label for="id_hunter" class="form-label">Hunter (Opsional):</label>
                        <select name="id_hunter" id="id_hunter" class="form-select">
                            <option value="">Tidak Ada</option>
                            @foreach ($hunters as $hunter)
                                <option value="{{ $hunter->id_pegawai }}" {{ $transaction->id_hunter == $hunter->id_pegawai ? 'selected' : '' }}>
                                    {{ $hunter->nama_pegawai }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 info-group">
                        <label for="id_penitip" class="form-label">Penitip:</label>
                        <select name="id_penitip" id="id_penitip" class="form-select @error('id_penitip') is-invalid @endif" required>
                            @foreach ($penitips as $penitip)
                                <option value="{{ $penitip->id_penitip }}" {{ $transaction->id_penitip == $penitip->id_penitip ? 'selected' : '' }}>
                                    {{ $penitip->nama_penitip }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_penitip')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @endif
                    </div>
                    <div class="col-md-6 info-group">
                        <label for="tanggal_penitipan" class="form-label">Tanggal Masuk:</label>
                        <input type="date" name="tanggal_penitipan" id="tanggal_penitipan" class="form-control @error('tanggal_penitipan') is-invalid @endif" value="{{ old('tanggal_penitipan', $transaction->tanggal_penitipan_formatted) }}" required>
                        @error('tanggal_penitipan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Barang Items -->
        <div class="card shadow-lg border-0 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold text-primary mb-3"><i class="fas fa-boxes me-2"></i>Daftar Barang</h5>
                @foreach ($transaction->barang as $index => $item)
                    <div class="card mb-3 shadow-sm border-0">
                        <div class="card-body p-3 bg-light rounded-3">
                            <h6 class="fw-bold text-dark mb-3">Barang #{{ $index + 1 }}</h6>
                            <input type="hidden" name="items[{{ $index }}][id_barang]" value="{{ $item->id_barang }}">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="items_{{ $index }}_id_kategori" class="form-label">Kategori</label>
                                    <select name="items[{{ $index }}][id_kategori]" id="items_{{ $index }}_id_kategori" class="form-select @error("items.$index.id_kategori") is-invalid @endif" required>
                                        @foreach ($kategoris as $kategori)
                                            <option value="{{ $kategori->id_kategori }}" {{ $item->id_kategori == $kategori->id_kategori ? 'selected' : '' }}>
                                                {{ $kategori->nama_kategori }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error("items.$index.id_kategori")
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <label for="items_{{ $index }}_nama_barang" class="form-label">Nama Barang</label>
                                    <input type="text" name="items[{{ $index }}][nama_barang]" id="items_{{ $index }}_nama_barang" class="form-control @error("items.$index.nama_barang") is-invalid @endif" value="{{ old("items.$index.nama_barang", $item->nama_barang) }}" required>
                                    @error("items.$index.nama_barang")
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <label for="items_{{ $index }}_harga_barang" class="form-label">Harga</label>
                                    <input type="number" name="items[{{ $index }}][harga_barang]" id="items_{{ $index }}_harga_barang" class="form-control @error("items.$index.harga_barang") is-invalid @endif" value="{{ old("items.$index.harga_barang", $item->harga_barang) }}" step="0.01" required>
                                    @error("items.$index.harga_barang")
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <label for="items_{{ $index }}_berat_barang" class="form-label">Berat (kg)</label>
                                    <input type="number" name="items[{{ $index }}][berat_barang]" id="items_{{ $index }}_berat_barang" class="form-control @error("items.$index.berat_barang") is-invalid @endif" value="{{ old("items.$index.berat_barang", $item->berat_barang) }}" step="0.01" required>
                                    @error("items.$index.berat_barang")
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @endif
                                </div>
                                <div class="col-md-12">
                                    <label for="items_{{ $index }}_deskripsi_barang" class="form-label">Deskripsi</label>
                                    <textarea name="items[{{ $index }}][deskripsi_barang]" id="items_{{ $index }}_deskripsi_barang" class="form-control @error("items.$index.deskripsi_barang") is-invalid @endif" required>{{ old("items.$index.deskripsi_barang", $item->deskripsi_barang) }}</textarea>
                                    @error("items.$index.deskripsi_barang")
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <label for="items_{{ $index }}_status_garansi" class="form-label">Status Garansi</label>
                                    <select name="items[{{ $index }}][status_garansi]" id="items_{{ $index }}_status_garansi" class="form-select @error("items.$index.status_garansi") is-invalid @endif status-garansi" data-index="{{ $index }}" required>
                                        <option value="berlaku" {{ $item->status_garansi == 'garansi' ? 'selected' : '' }}>Garansi</option>
                                        <option value="tidak" {{ $item->status_garansi == 'tidak garansi' ? 'selected' : '' }}>Tidak Garansi</option>
                                    </select>
                                    @error("items.$index.status_garansi")
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <label for="items_{{ $index }}_tanggal_garansi" class="form-label">Tanggal Garansi</label>
                                    <input type="date" name="items[{{ $index }}][tanggal_garansi]" id="items_{{ $index }}_tanggal_garansi" class="form-control tanggal-garansi @error("items.$index.tanggal_garansi") is-invalid @endif" value="{{ old("items.$index.tanggal_garansi", $item->tanggal_garansi_formatted) }}" {{ $item->status_garansi == 'garansi' ? 'required' : 'disabled' }}>
                                    @error("items.$index.tanggal_garansi")
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <label for="items_{{ $index }}_tanggal_berakhir" class="form-label">Tanggal Berakhir</label>
                                    <input type="date" name="items[{{ $index }}][tanggal_berakhir]" id="items_{{ $index }}_tanggal_berakhir" class="form-control @error("items.$index.tanggal_berakhir") is-invalid @endif" value="{{ old("items.$index.tanggal_berakhir", $item->tanggal_berakhir_formatted) }}" required>
                                    @error("items.$index.tanggal_berakhir")
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <label for="items_{{ $index }}_status_barang" class="form-label">Status Barang</label>
                                    <select name="items[{{ $index }}][status_barang]" id="items_{{ $index }}_status_barang" class="form-select @error("items.$index.status_barang") is-invalid @endif" required>
                                        <option value="tersedia" {{ $item->status_barang == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                                        <option value="selesai" {{ $item->status_barang == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                        <option value="sedang dikirim" {{ $item->status_barang == 'sedang dikirim' ? 'selected' : '' }}>Sedang Dikirim</option>
                                        <option value="menunggu pengambilan" {{ $item->status_barang == 'menunggu pengambilan' ? 'selected' : '' }}>Menunggu Pengambilan</option>
                                        <option value="diproses" {{ $item->status_barang == 'diproses' ? 'selected' : '' }}>Diproses</option>
                                        <option value="dibatalkan" {{ $item->status_barang == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                                        <option value="menunggu pembayaran" {{ $item->status_barang == 'menunggu pembayaran' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                                        <option value="didonasikan" {{ $item->status_barang == 'didonasikan' ? 'selected' : '' }}>Didonasikan</option>
                                        <option value="barang untuk donasi" {{ $item->status_barang == 'barang untuk donasi' ? 'selected' : '' }}>Barang untuk Donasi</option>
                                    </select>
                                    @error("items.$index.status_barang")
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <label for="items_{{ $index }}_images" class="form-label">Upload Gambar Baru (Min 2, Max 2048KB)</label>
                                    <input type="file" name="items[{{ $index }}][images][]" id="items_{{ $index }}_images" class="form-control @error("items.$index.images") is-invalid @endif" multiple>
                                    @error("items.$index.images")
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @endif
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Gambar Existing</label>
                                    <div class="row g-2" id="image-preview-{{ $index }}">
                                        @forelse ($item->gambar as $gambar)
                                            <div class="col-6 col-md-4 mb-2 image-item" data-id="{{ $gambar->id_gambar }}">
                                                <div class="card h-100 border-0 shadow-sm">
                                                    <img src="{{ asset('storage/gambar/' . $gambar->gambar_barang) }}" class="card-img-top img-fluid rounded-3" alt="{{ $item->nama_barang }}" style="height: 100px; object-fit: cover;">
                                                    <div class="card-body p-2">
                                                        <div class="form-check">
                                                            <input type="checkbox" name="items[{{ $index }}][delete_images][]" value="{{ $gambar->id_gambar }}" class="form-check-input delete-checkbox" id="delete_image_{{ $gambar->id_gambar }}">
                                                            <label class="form-check-label" for="delete_image_{{ $gambar->id_gambar }}">Hapus</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <p class="text-muted">Tidak ada gambar tersedia.</p>
                                        @endforelse
                                    </div>
                                    <div id="image-count-{{ $index }}" class="mt-2"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Submit Button -->
        <div class="text-end">
            <button type="submit" class="btn btn-primary rounded-pill" id="submitUpdate">
                <i class="fas fa-save me-2"></i>Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
    body, .card, .btn, .badge, p, h1, h2, h3, h4, h5, h6 {
        font-family: 'Poppins', sans-serif;
    }

    .card {
        border-radius: 15px;
        overflow: hidden;
    }

    .card-hover {
        transition: all 0.3s ease;
    }

    .card-hover:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
    }

    .bg-gradient-primary {
        background: linear-gradient(135deg, #007bff, #00b4d8);
    }

    .badge {
        padding: 0.6em 1.2em;
        font-weight: 600;
        border-radius: 20px;
    }

    .btn-sm {
        border-radius: 20px;
        padding: 0.25rem 1rem;
        font-size: 0.875rem;
    }

    .img-fluid {
        transition: transform 0.3s ease;
    }

    .img-fluid:hover {
        transform: scale(1.1);
    }

    .bg-light {
        background-color: #f8f9fa;
    }

    .invalid-feedback {
        display: block;
    }

    .form-check-label {
        margin-left: 5px;
    }

    .image-item {
        position: relative;
        transition: opacity 0.3s ease;
    }

    .image-item.hidden {
        opacity: 0.3;
        pointer-events: none;
    }

    /* Styling untuk Informasi Transaksi */
    .info-group {
        margin-bottom: 1.5rem;
    }

    .info-group .form-label {
        font-weight: 600;
        margin-bottom: 0.5rem;
        display: block;
    }

    .info-group .form-select,
    .info-group .form-control {
        width: 100%;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Preview gambar baru saat diupload
        document.querySelectorAll('input[type="file"]').forEach(input => {
            input.addEventListener('change', function(e) {
                const index = this.id.split('_')[1];
                const preview = document.getElementById(`image-preview-${index}`);
                const imageCount = document.getElementById(`image-count-${index}`);
                const files = e.target.files;

                Array.from(files).forEach((file, i) => {
                    const validImageExtensions = ['jpg', 'jpeg', 'png'];
                    const fileExtension = file.name.split('.').pop().toLowerCase();
                    const isImage = validImageExtensions.includes(fileExtension) || file.type.startsWith('image/');
                    const maxSize = 2 * 1024 * 1024;

                    if (!isImage) {
                        alert(`File ${file.name} bukan gambar. Harap unggah file gambar (jpeg, png, jpg).`);
                        return;
                    }

                    if (file.size > maxSize) {
                        alert(`File ${file.name} terlalu besar. Maksimal 2MB per gambar.`);
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const imgContainer = document.createElement('div');
                        imgContainer.className = 'col-6 col-md-4 mb-2 image-item';
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.alt = `Pratinjau gambar baru ${i + 1} untuk item ${index}`;
                        img.className = 'card-img-top img-fluid rounded-3';
                        img.style.height = '100px';
                        img.style.objectFit = 'cover';
                        imgContainer.appendChild(img);
                        preview.appendChild(imgContainer);
                    };
                    reader.readAsDataURL(file);
                });

                if (files.length < 2 && files.length > 0) {
                    imageCount.innerHTML = `<small class="text-danger">Jumlah gambar: ${files.length} (Minimal 2 gambar diperlukan)</small>`;
                } else {
                    imageCount.innerHTML = `<small class="text-success">Jumlah gambar: ${files.length}</small>`;
                }
            });
        });

        // Hapus preview saat checkbox dicentang
        document.querySelectorAll('.delete-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function(e) {
                const imageItem = this.closest('.image-item');
                if (this.checked) {
                    imageItem.classList.add('hidden');
                } else {
                    imageItem.classList.remove('hidden');
                }
            });
        });

        // Konfirmasi sebelum submit
        document.getElementById('submitUpdate').addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin menyimpan perubahan ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('updateTransactionForm').submit();
                }
            });
        });

        // Enable/disable tanggal garansi berdasarkan status garansi
        document.querySelectorAll('.status-garansi').forEach(select => {
            const index = select.dataset.index;
            const tanggalGaransiInput = document.getElementById(`items_${index}_tanggal_garansi`);

            // Initial state
            if (select.value === 'tidak') {
                tanggalGaransiInput.disabled = true;
                tanggalGaransiInput.value = ''; // Kosongkan jika tidak garansi
                tanggalGaransiInput.removeAttribute('required');
            } else {
                tanggalGaransiInput.disabled = false;
                tanggalGaransiInput.setAttribute('required', 'required');
            }

            // On change
            select.addEventListener('change', function() {
                if (this.value === 'tidak') {
                    tanggalGaransiInput.disabled = true;
                    tanggalGaransiInput.value = ''; // Kosongkan jika tidak garansi
                    tanggalGaransiInput.removeAttribute('required');
                } else {
                    tanggalGaransiInput.disabled = false;
                    tanggalGaransiInput.setAttribute('required', 'required');
                }
            });
        });
    });
</script>
@endpush