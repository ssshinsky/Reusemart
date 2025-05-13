@extends('layouts.admin')

@section('title', 'Edit Penitip')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">✏️ Edit Data Penitip</h2>
    <form action="{{ route('penitip.update', $penitip->id_penitip) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="nama" class="form-label">Nama Penitip</label>
                <input type="text" name="nama" id="nama" class="form-control" value="{{ old('nama', $penitip->user->nama) }}" required>
            </div>
            <div class="col-md-6">
                <label for="email" class="form-label">Email Penitip</label>
                <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $penitip->user->email) }}" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="telepon" class="form-label">Nomor Telepon</label>
                <input type="text" name="telepon" id="telepon" class="form-control" value="{{ old('telepon', $penitip->user->telepon) }}" required>
            </div>
            <div class="col-md-6">
                <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir', $penitip->user->tanggal_lahir) }}" required>
            </div>
        </div>

        <div class="mb-3">
            <label for="alamat" class="form-label">Alamat</label>
            <textarea name="alamat" id="alamat" class="form-control" rows="3" required>{{ old('alamat', $penitip->user->alamat) }}</textarea>
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('penitip.index') }}" class="btn btn-secondary">← Kembali</a>
            <button type="submit" class="btn btn-primary">💾 Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
