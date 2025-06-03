@extends('gudang.gudang_layout')

@section('title', 'Tambah Transaksi Barang Titipan')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="main-header">
        <div>
            <h2 class="fw-bold text-dark">Tambah Transaksi Barang Titipan</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('gudang.dashboard') }}">Gudang</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tambah Transaksi</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card">
        <form id="transaction-form" action="{{ route('gudang.store.transaction') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-4">
                <div class="col-md-6">
                    <label for="penitip-select" class="form-label text-muted fw-medium">Pilih Penitip</label>
                    <select name="id_penitip" id="penitip-select" class="form-select rounded-lg" required>
                        <option value="">Pilih Penitip</option>
                        @foreach ($penitips as $penitip)
                            <option value="{{ $penitip->id_penitip }}">{{ $penitip->nama_penitip }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="qc-select" class="form-label text-muted fw-medium">Petugas QC</label>
                    <select name="id_qc" id="qc-select" class="form-select rounded-lg" required>
                        <option value="">Pilih Petugas QC</option>
                        @foreach ($qcs as $qc)
                            <option value="{{ $qc->id_pegawai }}">{{ $qc->nama_pegawai }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="hunter-select" class="form-label text-muted fw-medium">Hunter (Opsional)</label>
                    <select name="id_hunter" id="hunter-select" class="form-select rounded-lg">
                        <option value="">Pilih Hunter (Opsional)</option>
                        @foreach ($hunters as $hunter)
                            <option value="{{ $hunter->id_pegawai }}">{{ $hunter->nama_pegawai }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="tanggal_masuk" class="form-label text-muted fw-medium">Tanggal Masuk Gudang</label>
                    <input type="date" name="tanggal_masuk" id="tanggal_masuk" class="form-control rounded-lg" required value="{{ now()->format('Y-m-d') }}">
                </div>
                <div class="col-md-12">
                    <h5 class="fw-bold text-dark">Daftar Barang</h5>
                    <button type="button" id="add-item" class="btn btn-primary mb-3">Tambah Barang</button>
                    <div id="items-container">
                        <div class="item-row">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="kategori-select-0" class="form-label text-muted fw-medium">Kategori</label>
                                    <select name="items[0][id_kategori]" id="kategori-select-0" class="form-select rounded-lg" required>
                                        <option value="">Pilih Kategori</option>
                                        @foreach ($kategoris as $kategori)
                                            <option value="{{ $kategori->id_kategori }}">{{ $kategori->nama_kategori }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="nama_barang-0" class="form-label text-muted fw-medium">Nama Barang</label>
                                    <input type="text" name="items[0][nama_barang]" id="nama_barang-0" class="form-control rounded-lg" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="harga_barang-0" class="form-label text-muted fw-medium">Harga Barang (Rp)</label>
                                    <input type="number" name="items[0][harga_barang]" id="harga_barang-0" class="form-control rounded-lg" min="1" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="berat_barang-0" class="form-label text-muted fw-medium">Berat Barang (kg)</label>
                                    <input type="number" name="items[0][berat_barang]" id="berat_barang-0" class="form-control rounded-lg" step="0.01" min="0.01" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="deskripsi_barang-0" class="form-label text-muted fw-medium">Deskripsi Barang</label>
                                    <textarea name="items[0][deskripsi_barang]" id="deskripsi_barang-0" class="form-control rounded-lg" rows="2" maxlength="255" required></textarea>
                                </div>
                                <div class="col-md-4">
                                    <label for="garansi-0" class="form-label text-muted fw-medium">Status Garansi</label>
                                    <select name="items[0][status_garansi]" id="garansi-0" class="form-select rounded-lg" required>
                                        <option value="" disabled selected>Pilih Status Garansi</option>
                                        <option value="berlaku">Garansi</option>
                                        <option value="tidak">Tidak Garansi</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="tanggal_garansi-0" class="form-label text-muted fw-medium">Tanggal Berakhir Garansi</label>
                                    <input type="date" name="items[0][tanggal_garansi]" id="tanggal_garansi-0" class="form-control rounded-lg" disabled>
                                </div>
                                <div class="col-md-4">
                                    <label for="images-0" class="form-label text-muted fw-medium">Unggah Gambar (Min 2)</label>
                                    <input type="file" name="items[0][images][]" id="images-0" class="form-control rounded-lg" multiple accept="image/jpeg,image/png,image/jpg" onchange="previewImages(event, 0)" required>
                                    <small class="text-muted">Unggah minimal 2 gambar (jpeg, png, jpg, maks 2MB per gambar)</small>
                                </div>
                                <div class="col-md-12">
                                    <div id="image-preview-0" class="image-preview-container"></div>
                                    <div id="image-count-0" class="mt-2"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-success px-4 py-2">
                    <i class="bi bi-check-circle me-2"></i> Simpan Transaksi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
    .form-select, .form-control, textarea {
        border-color: var(--border-color);
        transition: all 0.3s;
    }

    .form-select:focus, .form-control:focus, textarea:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(0, 177, 79, 0.25);
    }

    .form-label {
        font-size: 0.9rem;
    }

    .btn-success {
        border-radius: 10px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-success:hover {
        background-color: var(--secondary-color);
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 177, 79, 0.3);
    }

    .card {
        border: none;
        border-radius: 16px;
        padding: 24px;
        background-color: #ffffff;
        box-shadow: var(--card-shadow);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    }

    #items-container .item-row {
        margin-bottom: 20px;
        padding: 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
    }

    .image-preview-container {
        display: flex;
        overflow-x: auto;
        gap: 10px;
        padding: 5px;
        white-space: nowrap;
    }

    .image-preview-container img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid var(--border-color);
    }

    .image-preview-container img:hover {
        opacity: 0.9;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    let uploadedImages = {};
    let itemCount = 0;

    document.addEventListener('DOMContentLoaded', () => {
        uploadedImages[0] = [];

        document.getElementById('add-item').addEventListener('click', () => {
            itemCount++;
            const container = document.getElementById('items-container');
            const newRow = document.createElement('div');
            newRow.className = 'item-row';
            newRow.innerHTML = `
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="kategori-select-${itemCount}" class="form-label text-muted fw-medium">Kategori</label>
                        <select name="items[${itemCount}][id_kategori]" id="kategori-select-${itemCount}" class="form-select rounded-lg" required>
                            <option value="">Pilih Kategori</option>
                            @foreach ($kategoris as $kategori)
                                <option value="{{ $kategori->id_kategori }}">{{ $kategori->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="nama_barang-${itemCount}" class="form-label text-muted fw-medium">Nama Barang</label>
                        <input type="text" name="items[${itemCount}][nama_barang]" id="nama_barang-${itemCount}" class="form-control rounded-lg" required>
                    </div>
                    <div class="col-md-4">
                        <label for="harga_barang-${itemCount}" class="form-label text-muted fw-medium">Harga Barang (Rp)</label>
                        <input type="number" name="items[${itemCount}][harga_barang]" id="harga_barang-${itemCount}" class="form-control rounded-lg" min="1" required>
                    </div>
                    <div class="col-md-4">
                        <label for="berat_barang-${itemCount}" class="form-label text-muted fw-medium">Berat Barang (kg)</label>
                        <input type="number" name="items[${itemCount}][berat_barang]" id="berat_barang-${itemCount}" class="form-control rounded-lg" step="0.01" min="0.01" required>
                    </div>
                    <div class="col-md-4">
                        <label for="deskripsi_barang-${itemCount}" class="form-label text-muted fw-medium">Deskripsi Barang</label>
                        <textarea name="items[${itemCount}][deskripsi_barang]" id="deskripsi_barang-${itemCount}" class="form-control rounded-lg" rows="2" maxlength="255" required></textarea>
                    </div>
                    <div class="col-md-4">
                        <label for="garansi-${itemCount}" class="form-label text-muted fw-medium">Status Garansi</label>
                        <select name="items[${itemCount}][status_garansi]" id="garansi-${itemCount}" class="form-select rounded-lg" required>
                            <option value="" disabled selected>Pilih Status Garansi</option>
                            <option value="berlaku">Garansi</option>
                            <option value="tidak">Tidak Garansi</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="tanggal_garansi-${itemCount}" class="form-label text-muted fw-medium">Tanggal Berakhir Garansi</label>
                        <input type="date" name="items[${itemCount}][tanggal_garansi]" id="tanggal_garansi-${itemCount}" class="form-control rounded-lg" disabled>
                    </div>
                    <div class="col-md-4">
                        <label for="images-${itemCount}" class="form-label text-muted fw-medium">Unggah Gambar (Min 2)</label>
                        <input type="file" name="items[${itemCount}][images][]" id="images-${itemCount}" class="form-control rounded-lg" multiple accept="image/jpeg,image/png,image/jpg" onchange="previewImages(event, ${itemCount})" required>
                        <small class="text-muted">Unggah minimal 2 gambar (jpeg, png, jpg, maks 2MB per gambar)</small>
                    </div>
                    <div class="col-md-12">
                        <div id="image-preview-${itemCount}" class="image-preview-container"></div>
                        <div id="image-count-${itemCount}" class="mt-2"></div>
                    </div>
                    <div class="col-md-12 text-end">
                        <button type="button" class="btn btn-danger remove-item mt-2">Hapus Barang</button>
                    </div>
                </div>
            `;
            container.appendChild(newRow);

            uploadedImages[itemCount] = [];

            document.getElementById(`garansi-${itemCount}`).addEventListener('change', function() {
                const tanggalGaransiInput = document.getElementById(`tanggal_garansi-${itemCount}`);
                if (this.value === 'berlaku') {
                    tanggalGaransiInput.required = true;
                    tanggalGaransiInput.disabled = false;
                } else {
                    tanggalGaransiInput.required = false;
                    tanggalGaransiInput.disabled = true;
                    tanggalGaransiInput.value = '';
                }
            });

            newRow.querySelector('.remove-item').addEventListener('click', () => {
                newRow.remove();
                delete uploadedImages[itemCount];
            });
        });

        window.previewImages = function(event, index) {
            const preview = document.getElementById(`image-preview-${index}`);
            const imageCount = document.getElementById(`image-count-${index}`);
            const files = event.target.files;

            Array.from(files).forEach((file, i) => {
                const validImageExtensions = ['jpg', 'jpeg', 'png'];
                const fileExtension = file.name.split('.').pop().toLowerCase();
                const isImage = validImageExtensions.includes(fileExtension) || file.type.startsWith('image/');
                const maxSize = 2 * 1024 * 1024;

                if (!isImage) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Kesalahan',
                        text: `File ${file.name} bukan gambar. Harap unggah file gambar (jpeg, png, jpg).`
                    });
                    return;
                }

                if (file.size > maxSize) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Kesalahan',
                        text: `File ${file.name} terlalu besar. Maksimal 2MB per gambar.`
                    });
                    return;
                }

                if (!uploadedImages[index].some(f => f.name === file.name)) {
                    uploadedImages[index].push(file);
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    const imgContainer = document.createElement('div');
                    imgContainer.className = 'd-inline-block';
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = `Pratinjau gambar ${uploadedImages[index].length} untuk item ${index}`;
                    imgContainer.appendChild(img);
                    preview.appendChild(imgContainer);
                };
                reader.onerror = function(e) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Kesalahan',
                        text: `Gagal membaca file ${file.name}. Pastikan file adalah gambar yang valid.`
                    });
                };
                reader.readAsDataURL(file);
            });

            if (uploadedImages[index].length < 2) {
                imageCount.innerHTML = `<small class="text-danger">Jumlah gambar: ${uploadedImages[index].length} (Minimal 2 gambar diperlukan)</small>`;
            } else {
                imageCount.innerHTML = `<small class="text-success">Jumlah gambar: ${uploadedImages[index].length}</small>`;
            }
        };

        document.getElementById('transaction-form').addEventListener('submit', async (e) => {
            e.preventDefault();

            const penitip = document.getElementById('penitip-select').value;
            const qc = document.getElementById('qc-select').value;
            const hunter = document.getElementById('hunter-select').value;
            const tanggalMasuk = document.getElementById('tanggal_masuk').value;

            let errorMessages = [];
            for (let i = 0; i <= itemCount; i++) {
                if (!document.getElementById(`kategori-select-${i}`)) continue;

                const kategori = document.getElementById(`kategori-select-${i}`).value;
                const namaBarang = document.getElementById(`nama_barang-${i}`).value;
                const hargaBarang = document.getElementById(`harga_barang-${i}`).value;
                const beratBarang = document.getElementById(`berat_barang-${i}`).value;
                const deskripsiBarang = document.getElementById(`deskripsi_barang-${i}`).value;
                const garansi = document.getElementById(`garansi-${i}`).value;
                const tanggalGaransi = document.getElementById(`tanggal_garansi-${i}`).value;

                if (!kategori) errorMessages.push(`Kategori untuk barang ke-${i + 1} belum dipilih.`);
                if (!namaBarang) errorMessages.push(`Nama barang ke-${i + 1} belum diisi.`);
                if (!hargaBarang) errorMessages.push(`Harga barang ke-${i + 1} belum diisi.`);
                if (!beratBarang) errorMessages.push(`Berat barang ke-${i + 1} belum diisi.`);
                if (!deskripsiBarang) errorMessages.push(`Deskripsi barang ke-${i + 1} belum diisi.`);
                if (!garansi) errorMessages.push(`Status garansi untuk barang ke-${i + 1} belum dipilih.`);

                if (parseInt(hargaBarang) <= 0) errorMessages.push(`Harga barang ke-${i + 1} harus lebih dari 0.`);
                if (parseFloat(beratBarang) <= 0) errorMessages.push(`Berat barang ke-${i + 1} harus lebih dari 0.`);

                if (garansi === 'berlaku' && !tanggalGaransi) {
                    errorMessages.push(`Tanggal berakhir garansi untuk barang ke-${i + 1} belum diisi.`);
                }

                if (!uploadedImages[i] || uploadedImages[i].length < 2) {
                    errorMessages.push(`Gambar untuk barang ke-${i + 1} kurang dari 2. Minimal 2 gambar diperlukan.`);
                }
            }

            if (errorMessages.length > 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Kolom Kosong atau Tidak Valid',
                    html: errorMessages.join('<br>'),
                    confirmButtonText: 'Perbaiki'
                });
                return;
            }

            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('id_penitip', penitip);
            formData.append('id_qc', qc);
            formData.append('id_hunter', hunter || '');
            formData.append('tanggal_masuk', tanggalMasuk);

            let itemIndex = 0;
            for (let i = 0; i <= itemCount; i++) {
                if (!document.getElementById(`kategori-select-${i}`)) continue;

                formData.append(`items[${itemIndex}][id_kategori]`, document.getElementById(`kategori-select-${i}`).value);
                formData.append(`items[${itemIndex}][nama_barang]`, document.getElementById(`nama_barang-${i}`).value);
                formData.append(`items[${itemIndex}][harga_barang]`, document.getElementById(`harga_barang-${i}`).value);
                formData.append(`items[${itemIndex}][berat_barang]`, document.getElementById(`berat_barang-${i}`).value);
                formData.append(`items[${itemIndex}][deskripsi_barang]`, document.getElementById(`deskripsi_barang-${i}`).value);
                formData.append(`items[${itemIndex}][status_garansi]`, document.getElementById(`garansi-${i}`).value);
                formData.append(`items[${itemIndex}][tanggal_garansi]`, document.getElementById(`tanggal_garansi-${i}`).value || '');

                if (uploadedImages[i]) {
                    uploadedImages[i].forEach((file, idx) => {
                        formData.append(`items[${itemIndex}][images][]`, file);
                    });
                }
                itemIndex++;
            }

            const result = await Swal.fire({
                title: 'Konfirmasi Data',
                html: `
                    <p><strong>Penitip:</strong> ${document.querySelector(`#penitip-select option[value="${penitip}"]`)?.textContent ?? 'Tidak Diketahui'}</p>
                    <p><strong>Petugas QC:</strong> ${document.querySelector(`#qc-select option[value="${qc}"]`)?.textContent ?? 'Tidak Diketahui'}</p>
                    <p><strong>Hunter:</strong> ${hunter ? document.querySelector(`#hunter-select option[value="${hunter}"]`)?.textContent ?? 'Tidak Diketahui' : 'Tidak Dipilih'}</p>
                    <p><strong>Tanggal Masuk:</strong> ${tanggalMasuk}</p>
                    <p><strong>Jumlah Barang:</strong> ${itemIndex}</p>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#00b14f',
                cancelButtonColor: '#dc3545',
                confirmButtonText: 'Ya, Simpan',
                cancelButtonText: 'Batal'
            });

            if (result.isConfirmed) {
                try {
                    const response = await fetch(document.getElementById('transaction-form').action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        await Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Transaksi berhasil disimpan!',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        // Buka nota di tab baru
                        window.open(`/gudang/print-note/${data.transaction_id}`, '_blank');
                        // Redirect ke daftar transaksi setelah 2 detik (sesuai timer Swal)
                        setTimeout(() => {
                            window.location.href = '{{ route('gudang.transaction.list') }}';
                        }, 2000);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: data.message || 'Terjadi kesalahan saat menyimpan transaksi.'
                        });
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Gagal mengirim data ke server: ' + error.message
                    });
                    console.error('Error:', error);
                }
            }
        });

        document.getElementById('garansi-0').addEventListener('change', function() {
            const tanggalGaransiInput = document.getElementById('tanggal_garansi-0');
            if (this.value === 'berlaku') {
                tanggalGaransiInput.required = true;
                tanggalGaransiInput.disabled = false;
            } else {
                tanggalGaransiInput.required = false;
                tanggalGaransiInput.disabled = true;
                tanggalGaransiInput.value = '';
            }
        });
    });
</script>
@endpush