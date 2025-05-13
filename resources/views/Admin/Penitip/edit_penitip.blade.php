@extends('Admin.admin')

@section('title', 'Edit Item Owner')

@section('content')
<h2 style="margin-bottom: 1.5rem;">Edit Item Owner</h2>

<form action="{{ route('admin.penitip.update', $penitip->id_penitip) }}" method="POST" class="form-container">
    @csrf
    @method('PUT')

    <div class="form-grid">
        <div class="form-column">
            <div class="form-group">
                <label for="nik_penitip">NIK</label>
                <input type="text" name="nik_penitip" id="nik_penitip" value="{{ old('nik_penitip', $penitip->nik_penitip) }}" required>
            </div>
            <div class="form-group">
                <label for="nama_penitip">Nama</label>
                <input type="text" name="nama_penitip" id="nama_penitip" value="{{ old('nama_penitip', $penitip->nama_penitip) }}" required>
            </div>
            <div class="form-group">
                <label for="email_penitip">Email</label>
                <input type="email" name="email_penitip" id="email_penitip" value="{{ old('email_penitip', $penitip->email_penitip) }}" required>
            </div>
        </div> 
        <div class="form-column">
            <div class="form-group">
                <label for="no_telp">Nomor Telepon</label>
                <input type="text" name="no_telp" id="no_telp" value="{{ old('no_telp', $penitip->no_telp) }}" required>
            </div>
            <div class="form-group">
                <label for="alamat">Alamat</label>
                <textarea name="alamat" id="alamat" rows="3" required>{{ old('alamat', $penitip->alamat) }}</textarea>
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
</style>
@endsection
