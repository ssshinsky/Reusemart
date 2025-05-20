<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ReUseMart</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
        }

        .banner {
            background-color: #2E7D32;
            color: white;
            padding: 40px;
            border-radius: 12px;
        }

        .category-card {
            width: 115px;
            padding: 8px;
            margin: 4px;
            border-radius: 12px;
            border: 1px solid #e0e0e0;
            background-color: #ffffff;
            transition: transform 0.3s, box-shadow 0.3s;
            text-align: center;
        }

        .category-card img {
            width: 50px;
            height: 50px;
            object-fit: contain;
            margin-bottom: 8px;
        }

        .category-text {
            font-size: 12px;
            color: #2E7D32;
            font-weight: 500;
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

        .modal-content {
            border-radius: 12px;
            padding: 2rem;
            background-color: #fff;
        }

        .modal-header {
            border-bottom: none;
        }

        .modal-footer {
            border-top: none;
        }

        .btn-toggle-role {
            border-radius: 20px;
        }

        nav {
            display: flex;
            gap: 1rem;
        }

        nav a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
        }

        nav a:hover {
            color: green;
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">
    @include('partials.navbar')

    <div class="container my-5">
        <div class="banner d-flex flex-wrap align-items-center justify-content-between">
            <div class="text-section" style="max-width: 600px;">
                <h1 class="fw-bold mb-2 display-4">Recycle for a Better Tomorrow</h1>
                <h4 class="mb-4">From You, For All of Us</h4>
            </div>
            <img src="/assets/images/banner.jpg" alt="Banner" class="img-fluid rounded shadow"
                style="max-height: 255px;">
        </div>
    </div>

    <div class="container mb-5">
        <h5 class="mb-3 fw-bold">BROWSE BY CATEGORY</h5>
        <div class="d-flex flex-wrap gap-3 justify-content-start">
            <!-- Category Card -->
            <div class="category-card text-center">
                <img src="/assets/images/laptop.png" alt="Electronics">
                <p class="category-text">Electronics & Gadgets</p>
            </div>
            <div class="category-card text-center">
                <img src="/assets/images/jacket.png" alt="Clothing">
                <p class="category-text">Clothing & Accessories</p>
            </div>
            <div class="category-card text-center">
                <img src="/assets/images/table.jpg" alt="Furniture">
                <p class="category-text">Household Furniture</p>
            </div>
            <div class="category-card text-center">
                <img src="/assets/images/book.png" alt="Books">
                <p class="category-text">Book & Stationery</p>
            </div>
            <div class="category-card text-center">
                <img src="/assets/images/gamepad.png" alt="Hobbies">
                <p class="category-text">Hobbies & Collectibles</p>
            </div>
            <div class="category-card text-center">
                <img src="/assets/images/stroller.png" alt="Kids">
                <p class="category-text">Baby & Kids’ Supplies</p>
            </div>
            <div class="category-card text-center">
                <img src="/assets/images/bike.png" alt="Automotive">
                <p class="category-text">Automotive & Accessories</p>
            </div>
            <div class="category-card text-center">
                <img src="/assets/images/lawnmower.png" alt="Outdoor">
                <p class="category-text">Outdoor Equipment</p>
            </div>
        </div>
    </div>

    <div class="container mb-5">
        <h5 class="mb-3 fw-bold">RECOMMENDED PRODUCTS</h5>
        <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-3">
        </div>
        <div class="text-center mt-3">
            <button class="btn btn-success">View All Recommendations</button>
        </div>
    </div>

    <div class="container mb-5">
        <h5 class="mb-3 fw-bold">ALL PRODUCT</h5>
        <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-3">
            @foreach ($barangTerbatas as $item)
                <div class="col">
                    <div class="card h-100 text-center p-2">
                        <img src="{{ asset('storage/gambar/' . ($item->gambar->first()->gambar_barang ?? 'default.png')) }}"
                            class="card-img-top" alt="{{ $item->nama_barang }}">
                        <div class="card-body d-flex flex-column">
                            <p class="card-title small">{{ $item->nama_barang }}</p>
                            <p class="fw-bold text-success">Rp{{ number_format($item->harga_barang, 0, ',', '.') }}</p>
                            <div class="mt-auto">
                                <a href="{{ route('umum.show', $item->id_barang) }}" class="btn btn-success btn-sm w-100">View Details</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="text-center mt-3">
            <a href="{{ route('produk.allproduct') }}" class="btn btn-success">View All Products</a>
        </div>
    </div>

    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered custom-modal">
            <div class="modal-content">
                <form action="{{ route('login.submit') }}" method="POST">
                    @csrf
                    <div class="text-center mb-3">
                        <img src="/assets/images/logoNoBg.png" alt="Logo" style="width: 80px;">
                    </div>

                    <div class="mb-3">
                        <input type="email" name="email" class="form-control" placeholder="Email" required>
                    </div>
                    <div class="mb-4">
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                    </div>

                    <button type="submit" class="btn btn-success w-100 mb-2">Login</button>

                    <div class="text-center">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#registerModal"
                            data-bs-dismiss="modal">Register</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered custom-modal">
            <div class="modal-content">
                @csrf
                <div class="text-center mb-3">
                    <img src="/assets/images/logoNoBg.png" alt="Logo" style="width: 80px;">
                </div>
                <div class="d-flex justify-content-center mb-4">
                    <button id="btn-customer" type="button" class="btn btn-outline-success me-2"
                        onclick="showForm('customer')">Customer</button>
                    <button id="btn-organization" type="button" class="btn btn-outline-success"
                        onclick="showForm('organization')">Organization</button>
                </div>

                <form id="form-customer" action="/pembeli" method="POST" style="display: none;">
                    @csrf
                    <input type="text" name="nama_pembeli" class="form-control mb-2" placeholder="Name" required>
                    <input type="email" name="email_pembeli" class="form-control mb-2" placeholder="Email"
                        required>
                    <input type="date" name="tanggal_lahir" class="form-control mb-2" required>
                    <input type="tel" name="nomor_telepon" class="form-control mb-2" placeholder="Phone Number"
                        required>
                    <input type="password" name="password" class="form-control mb-2" placeholder="Password"
                        required>
                    <input type="hidden" name="profil_pict" value="default.png">
                    <button type="submit" class="btn btn-success w-100 mb-2">Register as Customer</button>
                </form>

                <form id="form-organization" action="/organisasi" method="POST" style="display: none;">
                    @csrf
                    <input type="text" name="nama_organisasi" class="form-control mb-2"
                        placeholder="Organization Name" required>
                    <input type="email" name="email_organisasi" class="form-control mb-2" placeholder="Email"
                        required>
                    <input type="text" name="alamat" class="form-control mb-2" placeholder="Address" required>
                    <input type="tel" name="kontak" class="form-control mb-2" placeholder="Phone Number"
                        required>
                    <input type="password" name="password" class="form-control mb-2" placeholder="Password"
                        required>
                    <input type="hidden" name="profil_pict" value="default.png">
                    <button type="submit" class="btn btn-success w-100 mb-2">Register as Organization</button>
                </form>

                <div class="text-center">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal"
                        data-bs-dismiss="modal">Login</a>
                </div>
            </div>
        </div>
    </div>

    @include('partials.footer')

    <script>
        function showForm(type) {
            const btnCustomer = document.getElementById('btn-customer');
            const btnOrganization = document.getElementById('btn-organization');
            const formCustomer = document.getElementById('form-customer');
            const formOrganization = document.getElementById('form-organization');

            formCustomer.style.display = (type === 'customer') ? 'block' : 'none';
            formOrganization.style.display = (type === 'organization') ? 'block' : 'none';

            if (type === 'customer') {
                btnCustomer.classList.remove('btn-outline-success');
                btnCustomer.classList.add('btn-success');
                btnOrganization.classList.remove('btn-success');
                btnOrganization.classList.add('btn-outline-success');
            } else {
                btnOrganization.classList.remove('btn-outline-success');
                btnOrganization.classList.add('btn-success');
                btnCustomer.classList.remove('btn-success');
                btnCustomer.classList.add('btn-outline-success');
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            showForm('customer');
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>