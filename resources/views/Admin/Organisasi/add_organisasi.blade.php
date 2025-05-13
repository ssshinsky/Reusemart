@extends('Admin.admin')

@section('title', 'Add Organization')

@section('content')
<h2 style="margin-bottom: 1.5rem;">Add Organization</h2>

<form action="{{ route('admin.organisasi.store') }}" method="POST" class="form-container">
    @csrf

    <div class="form-grid">
        <div class="form-column">
            <div class="form-group">
                <label for="nama_organisasi">Name</label>
                <input type="text" name="nama_organisasi" id="nama_organisasi" placeholder="Enter organization name" value="{{ old('nama_organisasi') }}" required>
                @error('nama_organisasi') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="email_organisasi">Email</label>
                <input type="email" name="email_organisasi" id="email_organisasi" placeholder="Enter email" value="{{ old('email_organisasi') }}" required>
                @error('email_organisasi') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="kontak">Contact Person</label>
                <input type="text" name="kontak" id="kontak" placeholder="Enter contact name" value="{{ old('kontak') }}" required>
                @error('kontak') <div class="text-danger">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-column">
            <div class="form-group">
                <label for="alamat">Address</label>
                <textarea name="alamat" id="alamat" rows="4" placeholder="Enter address" required>{{ old('alamat') }}</textarea>
                @error('alamat') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Enter password" required>
                @error('password') <div class="text-danger">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>

    <div class="form-actions-container">
        <div class="form-actions">
            <a href="{{ route('admin.organisasi.index') }}" class="btn btn-cancel">Cancel</a>
            <button type="submit" class="btn btn-submit">Save</button>
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
        flex-wrap: wrap;
        gap: 2rem;
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
    .form-group textarea {
        padding: 0.6rem;
        font-family: inherit;
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

    .text-danger {
        color: #dc3545;
        font-size: 14px;
        margin-top: 4px;
    }
</style>
@endsection
