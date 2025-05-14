<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - ReUse Mart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #e9f5ec, #ffffff);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            max-width: 550px;
        }

        .card {
            border: none;
            border-radius: 12px;
        }

        .card-title {
            font-weight: 600;
            font-size: 1.25rem;
        }

        .form-label {
            font-weight: 500;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #ced4da;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-control:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.2);
        }

        .btn-success {
            border-radius: 8px;
            font-weight: 500;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .btn-success:hover {
            background-color: #218838;
            transform: translateY(-1px);
        }

        .card-body p {
            font-size: 0.9rem;
        }

        .logo-title {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        h2 {
            font-weight: 700;
            font-size: 1.8rem;
        }

        @media (max-width: 576px) {
            h2 {
                font-size: 1.5rem;
            }

            .card-body p {
                font-size: 0.85rem;
            }
        }
    </style>

</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="text-center mb-4 logo-title">
            <img src="/assets/images/logo.png" alt="ReUse Mart Logo" width="80">
            <h2 class="mb-0">Reset Password</h2>
        </div>

        <!-- STEP 1: Masukkan Email -->
        @if (!session('reset_email'))
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title text-success text-center">Step 1: Kirim Kode Verifikasi</h5>
                    <p class="text-muted text-center">Masukkan email Anda untuk menerima kode verifikasi.</p>
                    <form method="POST" action="{{ route('password.sendCode') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input type="email" name="email" class="form-control" placeholder="you@example.com"
                                required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Kirim Kode</button>
                    </form>
                </div>
            </div>

            <!-- STEP 2: Masukkan Kode Verifikasi -->
        @elseif (!session('reset_verified'))
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title text-success text-center">Step 2: Verifikasi Kode</h5>
                    <p class="text-muted text-center">Masukkan 6-digit kode yang dikirim ke email Anda.</p>
                    <form method="POST" action="{{ route('password.verifyCode') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="code" class="form-label">Kode Verifikasi</label>
                            <input type="text" name="kode" class="form-control" placeholder="6-digit code"
                                required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Verifikasi</button>
                    </form>
                </div>
            </div>

            <!-- STEP 3: Ubah Password -->
        @else
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title text-success text-center">Step 3: Ganti Password</h5>
                    <p class="text-muted text-center">Masukkan password baru Anda.</p>
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="password" class="form-label">Password Baru</label>
                            <input type="password" name="password" class="form-control" placeholder="Password Baru"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control"
                                placeholder="Konfirmasi Password" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Simpan Password</button>
                    </form>
                </div>
            </div>
        @endif
    </div>

    @if (session('success'))
        <script>
            alert("{{ session('success') }}");
            window.location.href = "/";
        </script>
    @endif

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
