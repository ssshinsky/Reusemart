@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3">
                <div class="text-center mb-4">
                    <img src="{{ asset('images/default-avatar.png') }}" alt="Avatar" class="rounded-circle" width="80">
                    <h6 class="mt-2">{{ $user['nama'] ?? 'User' }}</h6>
                    <a href="#" class="text-muted text-decoration-none">✏️ Edit Profile</a>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item fw-bold">My Account</li>
                    <li class="nav-item">
                        <a href="{{ route('pembeli.profile') }}" class="nav-link ps-3">Profile</a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link ps-3" data-bs-toggle="modal"
                            data-bs-target="#addAddressModal">Addresses</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('pembeli.password') }}" class="nav-link ps-3">Change Password</a>
                    </li>

                    <li class="nav-item mt-3 fw-bold">My Purchase</li>
                    <li class="nav-item">
                        <a href="{{ route('pembeli.purchase') }}" class="nav-link ps-3">Purchase History</a>
                    </li>

                    <li class="nav-item mt-2 fw-bold">My ReuseMart Coins</li>
                    <li class="nav-item">
                        <a href="{{ route('pembeli.reward') }}" class="nav-link ps-3">Reward Coins</a>
                    </li>
                </ul>
            </div>

            <!-- Main content -->
            <div class="col-md-9">
                <!-- Profile Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="text-success fw-bold">My Profile</h5>
                        <p class="text-muted">Manage and protect your account</p>
                        <form>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label>Username</label>
                                    <input type="text" class="form-control" placeholder="Username"
                                        value="{{ $user['nama'] ?? 'User' }}">
                                </div>
                                <div class="col-md-6">
                                    <label>Name</label>
                                    <input type="text" class="form-control" placeholder="Full Name"
                                        value="{{ $user['nama'] ?? 'User' }}">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label>Email</label>
                                    <input type="email" class="form-control" placeholder="Email Address"
                                        value="{{ auth()->user()->email }}">
                                </div>
                                <div class="col-md-6">
                                    <label>Phone Number</label>
                                    <input type="text" class="form-control" placeholder="Phone Number"
                                        value="{{ auth()->user()->nomor_telepon }}">
                                </div>
                            </div>

                            <div class="row mb-3 align-items-center">
                                <div class="col-md-6 d-flex align-items-center">
                                    <img src="{{ asset('images/default-avatar.png') }}" alt="Avatar"
                                        class="rounded-circle me-3" width="60">
                                    <div>
                                        <input type="file" class="form-control mb-1">
                                        <small>File size: max 1MB | JPG, PNG</small>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success mt-2">Save Change</button>
                        </form>
                    </div>
                </div>

                <!-- Address Card -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="text-success fw-bold">Alamat Pengiriman</h5>
                        <p class="text-muted">Kelola alamat pengirimanmu</p>

                        <button class="btn btn-success btn-sm mb-3" data-bs-toggle="modal"
                            data-bs-target="#addAddressModal">
                            + Tambah Alamat Baru
                        </button>

                        @forelse ($alamat as $item)
                            <div class="card mb-2">
                                <div class="card-body">
                                    <strong>{{ $item->label_alamat }}</strong><br>
                                    {{ $item->nama_orang }} - {{ $item->nomor_telepon }}<br>
                                    {{ $item->alamat_lengkap }}<br>
                                    @if ($item->is_default)
                                        <span class="badge bg-primary">Default</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-muted">Belum ada alamat tersimpan.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Alamat -->
    <div class="modal fade" id="addAddressModal" tabindex="-1" aria-labelledby="addAddressModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="addAddressForm" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Alamat Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @csrf
                    <input type="hidden" name="id_pembeli" value="{{ auth()->user()->id_pembeli }}">

                    <div class="mb-3">
                        <label>Nama Penerima</label>
                        <input type="text" name="nama_orang" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Label Alamat</label>
                        <input type="text" name="label_alamat" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Alamat Lengkap</label>
                        <input type="text" name="alamat_lengkap" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Alamat Detail</label>
                        <input type="text" name="alamat_detail" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label>Keterangan</label>
                        <input type="text" name="keterangan" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label>Nomor Telepon</label>
                        <input type="text" name="nomor_telepon" class="form-control" required>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="is_default" id="is_default">
                        <label class="form-check-label" for="is_default">Jadikan sebagai alamat utama</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Script Tambah Alamat -->
    <script>
        document.getElementById('addAddressForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const isDefault = formData.get('is_default') === 'on' ? 1 : 0;

            fetch("{{ route('alamat.store') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json",
                    },
                    body: new URLSearchParams({
                        id_pembeli: formData.get("id_pembeli"),
                        nama_orang: formData.get("nama_orang"),
                        label_alamat: formData.get("label_alamat"),
                        alamat_lengkap: `${formData.get("alamat_lengkap")}, ${formData.get("alamat_detail")}, ${formData.get("keterangan")}`,
                        nomor_telepon: formData.get("nomor_telepon"),
                        kode_pos: '00000',
                        is_default: isDefault
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    alert("Alamat berhasil ditambahkan!");
                    location.reload();
                })
                .catch(error => {
                    console.error(error);
                    alert("Gagal menambahkan alamat.");
                });
        });
    </script>
@endsection
