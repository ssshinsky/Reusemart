@extends('Admin.admin')

@section('title', 'Edit Organization')

@section('content')
    <h2 style="margin-bottom: 1.5rem;">Edit Organization</h2>

    <form action="{{ route('admin.organisasi.update', $organisasi->id_organisasi) }}" method="POST" class="form-container">
        @csrf
        @method('PUT')

        <div class="form-grid">
            <!-- Kolom Kiri -->
            <div class="form-column">
                <div class="form-group">
                    <label for="nama_organisasi">Organization Name</label>
                    <input type="text" name="nama_organisasi" id="nama_organisasi"
                        value="{{ $organisasi->nama_organisasi }}" required>
                </div>

                <div class="form-group">
                    <label for="email_organisasi">Email</label>
                    <input type="email" name="email_organisasi" id="email_organisasi"
                        value="{{ $organisasi->email_organisasi }}" required>
                </div>

                <div class="form-group">
                    <label for="no_telp">Phone Number</label>
                    <input type="text" name="no_telp" id="no_telp" value="{{ $organisasi->no_telp }}" required>
                </div>
            </div>

            <!-- Kolom Kanan -->
            <div class="form-column">
                <div class="form-group">
                    <label for="alamat">Address</label>
                    <textarea name="alamat" id="alamat" rows="4" required>{{ $organisasi->alamat }}</textarea>
                </div>
            </div>
        </div>

        <div class="form-actions-container">
            <div class="form-actions">
                <a href="{{ route('admin.organisasi.index') }}" class="btn btn-cancel">Cancel</a>
                <button type="submit" class="btn btn-submit">Update</button>
            </div>
        </div>
    </form>

    <style>
        .form-container {
            width: 100%;
            padding: 2rem;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        .form-grid {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
        }

        .form-column {
            flex: 1;
            min-width: 300px;
        }

        .form-group {
            margin-bottom: 1.2rem;
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            margin-bottom: 0.4rem;
            font-weight: 600;
            font-size: 16px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: 0.6rem;
            font-size: 16px;
            font-weight: 400;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        .form-actions-container {
            margin-top: 2rem;
            display: flex;
            justify-content: flex-end;
        }

        .form-actions {
            display: flex;
            gap: 1rem;
        }

        .btn {
            height: 48px;
            padding: 0 1.5rem;
            font-size: 16px;
            font-weight: 400;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-cancel {
            background-color: #dc3545;
            color: white;
            text-decoration: none;
        }

        .btn-submit {
            background-color: #28a745;
            color: white;
        }

        .btn-reset {
            background-color: #e53e3e;
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            margin-top: 0.5rem;
        }

        .btn-reset:hover,
        .btn:hover {
            opacity: 0.95;
        }

        @media (max-width: 768px) {
            .form-grid {
                flex-direction: column;
            }

            .form-actions-container {
                justify-content: center;
            }
        }
    </style>
@endsection
