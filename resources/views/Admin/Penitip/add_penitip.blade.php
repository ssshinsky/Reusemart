@extends('Admin.admin')

@section('title', 'Add Item Owner')

@section('content')
<h2 style="margin-bottom: 1.5rem;">Add Item Owner</h2>

<form action="{{ route('admin.penitip.store') }}" method="POST" class="form-container">
    @csrf

    <div class="form-grid">
        <div class="form-column">
            <div class="form-group">
                <label for="nik_penitip">NIK</label>
                <input type="text" name="nik_penitip" id="nik_penitip" required placeholder="Enter NIK"
                    value="{{ old('nik_penitip') }}">
                @error('nik_penitip') <div class="text-danger">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label for="nama_penitip">Name</label>
                <input type="text" name="nama_penitip" id="nama_penitip" required placeholder="Enter name"
                    value="{{ old('nama_penitip') }}">
                @error('nama_penitip') <div class="text-danger">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label for="email_penitip">Email</label>
                <input type="email" name="email_penitip" id="email_penitip" required placeholder="Enter email"
                    value="{{ old('email_penitip') }}">
                @error('email_penitip') <div class="text-danger">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label for="no_telp">Phone Number</label>
                <input type="text" name="no_telp" id="no_telp" required placeholder="Enter phone number"
                    value="{{ old('no_telp') }}">
                @error('no_telp') <div class="text-danger">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-column">
            <div class="form-group">
                <label for="alamat">Address</label>
                <textarea name="alamat" placeholder="Enter address" class="form-control" rows="4">{{ old('alamat') }}</textarea>
                @error('alamat') <div class="text-danger">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required placeholder="Enter password">
                @error('password') <div class="text-danger">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>

    <div class="form-actions-container">
        <div class="form-actions">
            <a href="{{ route('admin.penitip.index') }}" class="btn btn-cancel">Cancel</a>
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
        color: red;
        font-size: 0.9rem;
        margin-top: 0.2rem;
    }

</style>
@endsection
