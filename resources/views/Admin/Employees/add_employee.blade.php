@extends('Admin.admin')

@section('title', 'Add Employee')

@section('content')
    <h2 style="margin-bottom: 1.5rem;">Add Employee</h2>

    <form action="{{ route('admin.employees.store') }}" method="POST" class="form-container" novalidate id="employeeForm">
        @csrf

        @if ($errors->any())
            <div class="alert alert-danger" style="margin-bottom: 1.5rem; padding: 1rem; background-color: #f8d7da; color: #721c24; border-radius: 6px;">
                <strong>Please correct the following errors:</strong>
                <ul style="margin-top: 0.5rem; padding-left: 1.5rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="form-grid">
            <!-- Kolom Kiri -->
            <div class="form-column">
                <div class="form-group">
                    <label for="id_role">Role</label>
                    <select name="id_role" id="id_role" required>
                        <option value="" disabled selected hidden>Select role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id_role }}" {{ old('id_role') == $role->id_role ? 'selected' : '' }}>{{ $role->nama_role }}</option>
                        @endforeach
                    </select>
                    @error('id_role') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="nama_pegawai">Name</label>
                    <input type="text" name="nama_pegawai" id="nama_pegawai" value="{{ old('nama_pegawai') }}" placeholder="Enter name" required>
                    @error('nama_pegawai') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="email_pegawai">Email</label>
                    <input type="email" name="email_pegawai" id="email_pegawai" placeholder="Enter email" value="{{ old('email_pegawai') }}" required>
                    @error('email_pegawai') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="nomor_telepon">Phone Number</label>
                    <input type="text" name="nomor_telepon" id="nomor_telepon" value="{{ old('nomor_telepon') }}" placeholder="08xxxxxxxxxx" required>
                    @error('nomor_telepon') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="gaji_pegawai">Salary</label>
                    <input type="text" name="gaji_pegawai" id="gaji_pegawai" value="{{ old('gaji_pegawai') }}" placeholder="Rp 0" required>
                    @error('gaji_pegawai') <div class="text-danger">{{ $message }}</div> @enderror
                </div>
            </div>

            <!-- Kolom Kanan -->
            <div class="form-column">
                <div class="form-group">
                    <label for="tanggal_lahir">Birth Date</label>
                    <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" id="tanggal_lahir" required>
                    @error('tanggal_lahir') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="alamat_pegawai">Address</label>
                    <textarea name="alamat_pegawai" id="alamat_pegawai" rows="4" placeholder="Enter address" required>{{ old('alamat_pegawai') }}</textarea>
                    @error('alamat_pegawai') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" placeholder="Create password" required>
                    <small id="passwordHelp" class="form-text text-muted" style="color: #dc3545;"></small>
                    @error('password') <div class="text-danger">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <!-- Button Save dan Cancel -->
        <div class="form-actions-container">
            <div class="form-actions">
                <a href="{{ route('admin.employees.index') }}" class="btn btn-cancel">Cancel</a>
                <button type="submit" class="btn btn-submit" id="submitBtn">Save</button>
            </div>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            console.log('add_employee script loaded'); // Debug log
            const form = document.getElementById('employeeForm');
            const passwordInput = document.getElementById('password');
            const helpText = document.getElementById('passwordHelp');
            const salaryInput = document.getElementById('gaji_pegawai');
            const phoneInput = document.getElementById('nomor_telepon');
            const nameInput = document.getElementById('nama_pegawai');

            if (!form) {
                console.error('Form #employeeForm not found'); // Debug log
                return;
            }

            function validateForm() {
                console.log('Validating form...'); // Debug log
                const requiredFields = form.querySelectorAll('[required]');
                let allFilled = true;
                let firstInvalid = null;
                let errors = [];

                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        allFilled = false;
                        if (!firstInvalid) firstInvalid = field;
                        errors.push(`${field.previousElementSibling.textContent} belum diisi.`);
                    }
                });

                if (!nameInput.value.match(/^[A-Za-z\s]{2,50}$/)) {
                    errors.push('Nama harus terdiri dari 2-50 karakter, hanya huruf dan spasi.');
                    if (!firstInvalid) firstInvalid = nameInput;
                }

                if (!phoneInput.value.match(/^08[0-9]{8,12}$/)) {
                    errors.push('Nomor telepon harus diawali 08 dan terdiri dari 10-13 digit.');
                    if (!firstInvalid) firstInvalid = phoneInput;
                }

                if (passwordInput.value.length < 8) {
                    errors.push('Kata sandi harus minimal 8 karakter.');
                    if (!firstInvalid) firstInvalid = passwordInput;
                }

                const salaryValue = salaryInput.value.replace(/[^0-9]/g, '');
                if (!salaryValue || parseInt(salaryValue) <= 0) {
                    errors.push('Gaji harus berupa angka positif.');
                    if (!firstInvalid) firstInvalid = salaryInput;
                }

                const birthDate = new Date(document.getElementById('tanggal_lahir').value);
                const minDate = new Date();
                minDate.setFullYear(minDate.getFullYear() - 18);
                if (birthDate > minDate) {
                    errors.push('Karyawan harus berusia minimal 18 tahun.');
                    if (!firstInvalid) firstInvalid = document.getElementById('tanggal_lahir');
                }

                if (errors.length > 0) {
                    console.log('Validation errors:', errors); // Debug log
                    Swal.fire({
                        icon: 'error',
                        title: 'Kolom Kosong atau Tidak Valid',
                        html: errors.join('<br>'),
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'Perbaiki'
                    });
                    if (firstInvalid) firstInvalid.focus();
                    return false;
                }
                console.log('Form validated successfully'); // Debug log
                return true;
            }

            form.addEventListener('submit', (e) => {
                e.preventDefault();
                console.log('Form submit triggered'); // Debug log

                if (!validateForm()) {
                    console.log('Form validation failed'); // Debug log
                    return;
                }

                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah Anda yakin ingin menambahkan data pegawai?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#dc3545',
                    confirmButtonText: 'Ya, Simpan',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    console.log('SweetAlert result:', result); // Debug log
                    if (result.isConfirmed) {
                        console.log('Submitting form...'); // Debug log
                        form.submit(); // Submit form secara default
                    } else {
                        console.log('Form submission cancelled'); // Debug log
                    }
                });
            });

            // Salary formatting
            salaryInput.addEventListener('input', function (e) {
                let rawValue = e.target.value.replace(/[^0-9]/g, '');
                if (rawValue) {
                    const formatted = new Intl.NumberFormat('id-ID').format(rawValue);
                    e.target.value = 'Rp ' + formatted;
                } else {
                    e.target.value = '';
                }
            });

            // Prevent invalid input for phone number
            phoneInput.addEventListener('input', function (e) {
                let value = e.target.value.replace(/[^0-9]/g, '');
                if (value.length > 13) {
                    value = value.slice(0, 13);
                }
                e.target.value = value;
            });

            // Prevent invalid input for name
            nameInput.addEventListener('input', function (e) {
                let value = e.target.value.replace(/[^A-Za-z\s]/g, '');
                if (value.length > 50) {
                    value = value.slice(0, 50);
                }
                e.target.value = value;
            });

            // Deactivate confirmation (for use in employee index page)
            window.confirmDeactivate = function(employeeId, employeeName) {
                console.log('Deactivate confirmation triggered for ID:', employeeId); // Debug log
                Swal.fire({
                    title: 'Konfirmasi',
                    text: 'Apakah Anda yakin ingin menonaktifkan data pegawai?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Nonaktifkan',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    console.log('Deactivate SweetAlert result:', result); // Debug log
                    if (result.isConfirmed) {
                        console.log('Deactivating employee...'); // Debug log
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/admin/employees/${employeeId}/deactivate`;
                        const methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        methodInput.value = 'PUT';
                        const csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = '{{ csrf_token() }}';
                        form.appendChild(methodInput);
                        form.appendChild(csrfInput);
                        document.body.appendChild(form);
                        form.submit();
                    } else {
                        console.log('Deactivate cancelled'); // Debug log
                    }
                });
            };
        });

        @if ($errors->has('email_pegawai'))
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                text: 'Email sudah digunakan.',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
        @endif

        @if ($errors->any() && !$errors->has('email_pegawai'))
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                text: 'Silakan periksa kembali input Anda.',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
        @endif

        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ session('success') }}',
                timer: 2000,
                showConfirmButton: false
            });
        @endif
    </script>

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