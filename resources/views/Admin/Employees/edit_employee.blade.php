@extends('Admin.admin')

@section('title', 'Edit Employee')

@section('content')
<h2 style="margin-bottom: 1.5rem;">Edit Employee</h2>

<form action="{{ route('admin.employees.update', $pegawai->id_pegawai) }}" method="POST" class="form-container">
    @csrf
    @method('PUT')

    <div class="form-grid">
        <!-- Kolom Kiri -->
        <div class="form-column">
            <div class="form-group">
                <label for="id_role">Role</label>
                <select name="id_role" id="id_role" required>
                    @foreach($roles as $role)
                        <option value="{{ $role->id_role }}" {{ $pegawai->id_role == $role->id_role ? 'selected' : '' }}>
                            {{ $role->nama_role }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="nama_pegawai">Name</label>
                <input type="text" name="nama_pegawai" id="nama_pegawai" value="{{ $pegawai->nama_pegawai }}" required>
            </div>

            <div class="form-group">
                <label for="nomor_telepon">Phone Number</label>
                <input type="text" name="nomor_telepon" id="nomor_telepon" value="{{ $pegawai->nomor_telepon }}" required>
            </div>

            <div class="form-group">
                <label for="gaji_pegawai">Salary</label>
                <input type="text" name="gaji_pegawai" id="gaji_pegawai" value="Rp {{ number_format($pegawai->gaji_pegawai, 0, ',', '.') }}" required>
            </div>
        </div>

        <!-- Kolom Kanan -->
        <div class="form-column">
            <div class="form-group">
                <label for="tanggal_lahir">Birth Date</label>
                <input type="date" name="tanggal_lahir" id="tanggal_lahir"
                    value="{{ \Carbon\Carbon::parse($pegawai->tanggal_lahir)->format('Y-m-d') }}"
                    required>
            </div>    
            
            <div class="form-group">
                <label for="alamat_pegawai">Address</label>
                <textarea name="alamat_pegawai" id="alamat_pegawai" rows="4" required>{{ $pegawai->alamat_pegawai }}</textarea>
            </div>

            <div class="form-group">
                <label>Password</label>
                <button type="button" class="btn-reset" onclick="handleResetPassword()" style="width: 25%; background-color: #dc3545;">Reset Password</button>
            </div>
        </div>
    </div>

    <div class="form-actions-container">
        <div class="form-actions">
            <a href="{{ route('admin.employees.index') }}" class="btn btn-cancel">Cancel</a>
            <button type="submit" class="btn btn-submit">Update</button>
        </div>
    </div>
</form>

<script>
    // Format ulang salary saat diketik
    const salaryInput = document.getElementById('gaji_pegawai');
    salaryInput.addEventListener('input', function (e) {
        let rawValue = e.target.value.replace(/[^0-9]/g, '');
        if (rawValue) {
            e.target.value = 'Rp ' + new Intl.NumberFormat('id-ID').format(rawValue);
        } else {
            e.target.value = '';
        }
    });

    function handleResetPassword() {
        Swal.fire({
            title: 'Reset Password?',
            text: "Password akan di-reset ke format tanggal lahir (ddmmyyyy)",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e53e3e',
            cancelButtonColor: '#aaa',
            confirmButtonText: 'Reset'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`{{ route('admin.employees.reset-password', $pegawai->id_pegawai) }}`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                }).then(res => res.json())
                .then(data => {
                    Swal.fire('Success', data.message, 'success');
                }).catch(err => {
                    Swal.fire('Error', 'Gagal mereset password.', 'error');
                });
            }
        });
    }
</script>

<style>
    /* gunakan kembali styling dari halaman Add Employee */
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
        font-family: inherit;
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

    .btn-reset:hover {
        opacity: 0.95;
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
