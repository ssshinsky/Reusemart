@extends('layouts.main')

@section('content')
    <div class="container py-4">
        <div class="row">
            {{-- Sidebar --}}
            <div class="col-md-3">
                <div class="list-group shadow-sm sidebar-menu">
                    <a href="{{ route('penitip.profile') }}"
                        class="list-group-item {{ request()->routeIs('penitip.profile') ? 'active' : '' }}">
                        <i class="bi bi-person"></i> My Account
                    </a>
                    <a href="{{ route('penitip.myproduct') }}"
                        class="list-group-item {{ request()->routeIs('penitip.myproduct') ? 'active' : '' }}">
                        <i class="bi bi-box-seam"></i> My Product
                    </a>
                    <a href="{{ route('penitip.rewards') }}"
                        class="list-group-item {{ request()->routeIs('penitip.rewards') ? 'active' : '' }}">
                        <i class="bi bi-coin"></i> Balances and Rewards
                    </a>
                </div>
            </div>

            <div class="col-md-9">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row align-items-start">
                            <div class="col-md-3 text-center mb-3 mb-md-0">
                                <img src="{{ asset('storage/foto_penitip/' . ($penitip->profil_pict ?? 'default.png')) }}"
                                    alt="Profile Photo" class="rounded" width="120" height="120"
                                    style="object-fit: cover;">
                            </div>

                            <div class="col-md-9">
                                <h5 class="text-success fw-bold mb-1">My Profile</h5>
                                <p class="text-muted small mb-3">Manage and protect your account</p>

                                <div class="mb-3">
                                    <label class="form-label"><strong>NIK</strong></label>
                                    <input type="text" class="form-control" value="{{ $penitip->nik_penitip }}" disabled>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label"><strong>Name</strong></label>
                                    <input type="text" class="form-control" value="{{ $penitip->nama_penitip }}"
                                        disabled>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label"><strong>Email</strong></label>
                                    <input type="email" class="form-control" value="{{ $penitip->email_penitip }}"
                                        disabled>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label"><strong>Phone</strong></label>
                                    <input type="text" class="form-control" value="{{ $penitip->no_telp }}" disabled>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label"><strong>Address</strong></label>
                                    <textarea class="form-control" rows="3" disabled>{{ $penitip->alamat }}</textarea>
                                </div>
                            </div>


                            <div class="mb-3 text-end">
                                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                                    Edit Profile
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('penitip.update', $penitip->id_penitip) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nik_penitip" class="form-label">NIK</label>
                                <input type="text" id="nik_penitip" name="nik_penitip" class="form-control"
                                    value="{{ $penitip->nik_penitip }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="nama_penitip" class="form-label">Name</label>
                                <input type="text" id="nama_penitip" name="nama_penitip" class="form-control"
                                    value="{{ $penitip->nama_penitip }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email_penitip" class="form-label">Email</label>
                                <input type="email" id="email_penitip" name="email_penitip" class="form-control"
                                    value="{{ $penitip->email_penitip }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="no_telp" class="form-label">Phone Number</label>
                                <input type="text" id="no_telp" name="no_telp" class="form-control"
                                    value="{{ $penitip->no_telp }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="alamat" class="form-label">Address</label>
                            <textarea id="alamat" name="alamat" class="form-control" rows="3" required>{{ $penitip->alamat }}</textarea>
                        </div>

                        <div class="mb-3 d-flex align-items-center">
                            <img src="{{ asset('storage/foto_penitip/' . ($penitip->profil_pict ?? 'default.png')) }}"
                                alt="Profile Photo" class="rounded-circle me-3" width="80" height="80"
                                style="object-fit: cover;">
                            <div>
                                <label for="profil_pict" class="form-label d-block">Profile Photo</label>
                                <input type="file" id="profil_pict" name="profil_pict" accept=".jpg,.jpeg,.png"
                                    class="form-control">
                                <small class="text-muted">File max 1MB, JPG/PNG</small>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Save Changes</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #00b14f;
            --text-dark: #212529;
            --text-muted: #6c757d;
            --bg-light: #f8f9fa;
            --border-color: #dee2e6;
        }

        body {
            font-family: 'Poppins', sans-serif;
        }

        .text-success {
            color: var(--primary-color) !important;
        }

        .btn-success {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-success:hover {
            background-color: #019944;
            border-color: #019944;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            font-weight: 500;
            border: none;
            border-left: 4px solid transparent;
            color: #333;
            transition: all 0.3s;
        }

        .sidebar-menu a.active {
            background-color: var(--bg-light);
            border-left: 4px solid var(--primary-color);
            color: var(--primary-color);
        }

        .sidebar-menu a:hover {
            background-color: #f1f1f1;
            color: var(--primary-color);
        }

        .sidebar-menu i {
            margin-right: 10px;
        }

        .card {
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 24px;
        }

        .modal-content {
            border-radius: 16px;
            padding: 20px;
        }

        img.rounded-circle {
            border: 2px solid var(--primary-color);
        }
    </style>
@endpush
