@extends('layouts.main')

@section('content')
    <div class="container py-4">
        <div class="row">
            <div class="col-md-3">
                @include('pembeli.sidebar')
            </div>

            <div class="col-md-9">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-success">My Addresses</h5>
                    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addAddressModal">+ Add New
                        Address</button>
                </div>
                
                <!-- Form Pencarian -->
                <form method="GET" class="mb-4">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control"
                            placeholder="Cari Alamat"
                            value="{{ request('search') }}">
                        <button class="btn btn-outline-success" type="submit"><i class="bi bi-search"></i> Cari</button>
                    </div>
                </form>

                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                
                @forelse ($alamatList as $alamat)
                    <div class="card mb-3 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="fw-bold mb-1">{{ $alamat->nama_orang }}
                                        <span class="text-muted">(+62) {{ substr($alamat->no_telepon, -10) }}</span>
                                    </h6>
                                    <p class="mb-0">{{ $alamat->alamat_lengkap }} ({{ $alamat->label_alamat }})</p>
                                    <p class="mb-0 text-muted">{{ strtoupper($alamat->kecamatan) }},
                                        {{ strtoupper($alamat->kabupaten) }}, ID, {{ $alamat->kode_pos }}</p>
                                    @if ($alamat->is_default)
                                        <span class="badge bg-success mt-1">DEFAULT</span>
                                    @endif
                                </div>
                                <div class="text-end">
                                    <button class="btn btn-link text-decoration-none text-success p-0 mb-1"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editAddressModal{{ $alamat->id_alamat }}">Edit</button>
                                    <form action="{{ route('pembeli.alamat.destroy', $alamat->id_alamat) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('Delete this address?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="btn btn-link text-decoration-none text-danger p-0 mb-1">Delete</button>
                                    </form>
                                    @if (!$alamat->is_default)
                                        <form action="{{ route('pembeli.alamat.set_default', $alamat->id_alamat) }}"
                                            method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-secondary btn-sm">Set As Default</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="editAddressModal{{ $alamat->id_alamat }}" tabindex="-1"
                        aria-labelledby="editAddressModalLabel{{ $alamat->id_alamat }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('pembeli.alamat.update', $alamat->id_alamat) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editAddressModalLabel{{ $alamat->id_alamat }}">Edit
                                            Address</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Recipient's Name</label>
                                            <input type="text" name="nama_orang" class="form-control"
                                                value="{{ $alamat->nama_orang }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Address Label</label>
                                            <input type="text" name="label_alamat" class="form-control"
                                                value="{{ $alamat->label_alamat }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Full Address</label>
                                            <textarea name="alamat_lengkap" class="form-control" required>{{ $alamat->alamat_lengkap }}</textarea>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">District</label>
                                                <input type="text" name="kecamatan" class="form-control"
                                                    value="{{ $alamat->kecamatan }}">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">City</label>
                                                <input type="text" name="kabupaten" class="form-control"
                                                    value="{{ $alamat->kabupaten }}">
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Postal Code</label>
                                            <input type="text" name="kode_pos" class="form-control"
                                                value="{{ $alamat->kode_pos }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Phone</label>
                                            <input type="text" name="no_telepon" class="form-control"
                                                value="{{ $alamat->no_telepon }}">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-success">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="alert alert-warning">No addresses added yet.</div>
                @endforelse
            </div>
        </div>

        <div class="modal fade" id="addAddressModal" tabindex="-1" aria-labelledby="addAddressModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('pembeli.alamat.store') }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="addAddressModalLabel">Add New Address</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Recipient's Name</label>
                                <input type="text" name="nama_orang" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Address Label</label>
                                <input type="text" name="label_alamat" class="form-control"
                                    placeholder="e.g. Home, Office">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Full Address</label>
                                <textarea name="alamat_lengkap" class="form-control" required></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">District</label>
                                    <input type="text" name="kecamatan" class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">City</label>
                                    <input type="text" name="kabupaten" class="form-control">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Postal Code</label>
                                <input type="text" name="kode_pos" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" name="no_telepon" class="form-control">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Save Address</button>
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
    </style>
@endpush
