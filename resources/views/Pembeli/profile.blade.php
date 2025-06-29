@extends('layouts.main')

@section('content')
    <div class="container py-4">
        <div class="row">
            {{-- Sidebar Pembeli --}}
            <div class="col-md-3 mb-4">
                @include('Pembeli.sidebar')
            </div>

            <div class="col-md-9">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row g-4 align-items-center">
                            <div class="col-md-3 text-center mb-3 mb-md-0">
                                <img src="{{ asset('storage/foto_pembeli/' . ($pembeli->profil_pict ?? 'default.png')) }}"
                                    alt="Profile Photo" class="rounded" width="120" height="120"
                                    style="object-fit: cover;">
                            </div>

                            <div class="col-md-9">
                                <h5 class="fw-bold text-success mb-1">My Profile</h5>
                                <p class="text-muted small mb-3">Manage and protect your account</p>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Username</label>
                                    <input type="text" class="form-control" value="{{ $pembeli->nama_pembeli }}"
                                        disabled>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Email</label>
                                    <input type="email" class="form-control" value="{{ $pembeli->email_pembeli }}"
                                        disabled>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Phone</label>
                                    <input type="text" class="form-control" value="{{ $pembeli->nomor_telepon }}"
                                        disabled>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Poin Reward</label>
                                    <input type="text" class="form-control" value="{{ $pembeli->poin_pembeli ?? 0 }}" disabled>
                                </div>

                                <div class="text-end">
                                    <button class="btn btn-success" data-bs-toggle="modal"
                                        data-bs-target="#editProfileModal">
                                        Edit Profile
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Edit Profile --}}
        <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('pembeli.update', $pembeli->id_pembeli) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="modal-header">
                            <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="nama" class="form-label">Name</label>
                                    <input type="text" id="nama" name="nama" class="form-control"
                                        value="{{ $pembeli->nama_pembeli }}" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="nomor_telepon" class="form-label">Phone Number</label>
                                    <input type="text" id="nomor_telepon" name="nomor_telepon" class="form-control"
                                        value="{{ $pembeli->nomor_telepon }}" required>
                                </div>

                                <div class="col-12">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" id="email" name="email" class="form-control"
                                        value="{{ $pembeli->email_pembeli }}" required>
                                </div>

                                <div class="mb-3 d-flex align-items-center">
                                    <img src="{{ asset('storage/foto_pembeli/' . ($pembeli->profil_pict ?? 'default.png')) }}"
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
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Save Changes</button>
                        </div>
                    </form>
                </div>
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

        footer {
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            background-color: #2E7D32;
            color: white;
        }

        footer strong {
            font-weight: 600;
        }

        footer p,
        footer a {
            margin-bottom: 6px;
            line-height: 1.6;
            color: white;
            text-decoration: none;
        }

        footer a:hover {
            text-decoration: underline;
        }

        .footer-social a {
            font-size: 18px;
            margin-right: 12px;
            color: white;
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