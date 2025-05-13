@extends('Admin.admin')

@section('title', 'Add Employee')

@section('content')
    <h2 style="margin-bottom: 1.5rem;">Add Employee</h2>

    <form action="{{ route('admin.employees.store') }}" method="POST" class="form-container">
        @csrf

        <div class="form-grid">
            <!-- Kolom Kiri -->
            <div class="form-column">
                <div class="form-group">
                    <label for="id_role">Role</label>
                    <select name="id_role" id="id_role" required>
                        @foreach($roles as $role)
                            <option value="{{ $role->id_role }}" {{ old('id_role') == $role->id_role ? 'selected' : '' }}>{{ $role->nama_role }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="nama_pegawai">Name</label>
                    <input type="text" name="nama_pegawai" id="nama_pegawai" value="{{ old('nama_pegawai') }}" placeholder="Enter name" required>
                </div>

                <div class="form-group">
                    <label for="email_pegawai">Email</label>
                    <input type="email" name="email_pegawai" id="email_pegawai" placeholder="Enter email" required>
                </div>

                <div class="form-group">
                    <label for="nomor_telepon">Phone Number</label>
                    <input type="text" name="nomor_telepon" id="nomor_telepon" value="{{ old('nomor_telepon') }}" placeholder="08xxxxxxxxxx" required>
                </div>

                <div class="form-group">
                    <label for="gaji_pegawai">Salary</label>
                    <input type="text" name="gaji_pegawai" id="gaji_pegawai" value="{{ old('gaji_pegawai') }}" placeholder="Rp 0" required>
                </div>
            </div>
            
            <!-- Kolom Kanan -->
            <div class="form-column">
                <div class="form-group">
                    <label for="tanggal_lahir">Birth Date</label>
                    <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" id="tanggal_lahir" required>
                </div>

                <div class="form-group">
                    <label for="alamat_pegawai">Address</label>
                    <textarea name="alamat_pegawai" id="alamat_pegawai" rows="4" placeholder="Enter address" required>{{ old('alamat_pegawai') }}</textarea>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" placeholder="Create password" required>
                    <small id="passwordHelp" class="form-text text-muted" style="color: #dc3545;"></small>
                </div>
            </div>
        </div>

        <!-- Button Save dan Cancel -->
        <div class="form-actions-container">
            <div class="form-actions">
                <a href="{{ route('admin.employees.index') }}" class="btn btn-cancel">Cancel</a>
                <button type="submit" class="btn btn-submit" id="submitBtn" disabled>Save</button>
            </div>
        </div>
    </form>

    <script>
        // Setting Password
        const passwordInput = document.getElementById('password');
        const helpText = document.getElementById('passwordHelp');
        const submitBtn = document.getElementById('submitBtn');

        let hasInteracted = false;

        function validatePassword() {
            const value = passwordInput.value;
            if (value.length < 8) {
                if (hasInteracted) {
                    helpText.textContent = 'Password must be at least 8 characters.';
                }
                submitBtn.disabled = true;
            } else {
                helpText.textContent = '';
                submitBtn.disabled = false;
            }
        }

        passwordInput.addEventListener('input', () => {
            hasInteracted = true;
            validatePassword();
        });

        window.addEventListener('load', validatePassword);
        
        // Setting Salary
        const salaryInput = document.getElementById('gaji_pegawai');

        salaryInput.addEventListener('input', function (e) {
            let rawValue = e.target.value.replace(/[^0-9]/g, ''); // Hapus semua kecuali angka

            if (rawValue) {
                const formatted = new Intl.NumberFormat('id-ID').format(rawValue); // Format 3 digit
                e.target.value = 'Rp ' + formatted;
            } else {
                e.target.value = '';
            }
        });
    </script>


    @if ($errors->has('email_pegawai'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Validation Failed',
                text: 'Email is already in use.',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
        </script>
    @endif

    @if ($errors->any() && !$errors->has('email_pegawai'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Validation Failed',
                text: 'Please check your input again.',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
        </script>
    @endif

    
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
