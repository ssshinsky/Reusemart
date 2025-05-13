<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ReUseMart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        .banner {
            background-color: #fffbe6;
            padding: 20px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
        }

        .card img {
            object-fit: contain;
            height: 160px;
        }

        .card-title {
            font-size: 14px;
        }

        body {
            font-family: 'Poppins', sans-serif;
        }

        .category-card {
            min-width: 120px;
            border: 1px solid #ddd;
            border-radius: 12px;
            padding: 10px;
            transition: 0.3s;
            font-size: 12px;
        }

        .category-card:hover {
            background-color: #f8f9fa;
            transform: scale(1.02);
        }

        .category-card img {
            width: 60px;
            height: 60px;
            object-fit: contain;
        }

        footer {
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
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

        .custom-modal {
            max-width: 400px;
            width: 100%;
        }

        .modal-content {
            border-radius: 12px;
            padding: 2rem;
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

    <div class="container my-4">
        <div class="banner d-flex flex-wrap align-items-center">
            <div>
                <h3><strong>ReUseMart Special Offer</strong></h3>
                <p class="text-success fs-5">SAVE 50% on all second-hand clothing</p>
            </div>
            <div class="d-flex gap-3">
                <img src="/assets/images/phone.png" alt="Phone" width="100" height="100">
                <img src="/assets/images/sofa.png" alt="Sofa" width="100" height="100">
                <img src="/assets/images/shirt.png" alt="Shirt" width="100" height="100">
            </div>
        </div>
    </div>

    <div class="container mb-5">
        <h5 class="mb-3"><strong>BROWSE BY CATEGORY</strong></h5>
        <div class="d-flex flex-nowrap overflow-auto gap-3">
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
        <h5 class="mb-3"><strong>BROWSE BY CATEGORY</strong>x</h5>
        <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-3">
            <div class="col">
                <div class="card h-100 text-center p-2">
                    <img src="/assets/images/book.png" class="card-img-top" alt="Book">
                    <div class="card-body">
                        <p class="card-title small">Buku harry potter and the sorcerer's stone</p>
                        <p class="fw-bold text-success">Rp80.000</p>
                        <button class="btn btn-success btn-sm w-100">Buy Now</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center mt-3">
            <button class="btn btn-success">View All Recommendations</button>
        </div>
    </div>

    <div class="container mb-5">
        <h5 class="mb-3"><strong>ALL PRODUCT</strong></h5>
        <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-3">
            <div class="col">
                <div class="card h-100 text-center p-2">
                    <img src="/assets/images/book.png" class="card-img-top" alt="Book">
                    <div class="card-body">
                        <p class="card-title small">Buku harry potter and the sorcerer's stone</p>
                        <p class="fw-bold text-success">Rp80.000</p>
                        <button class="btn btn-success btn-sm w-100">Buy Now</button>
                    </div>
                </div>
            </div>

        </div>
        <div class="text-center mt-3">
            <button class="btn btn-success">View All Products</button>
        </div>
    </div>

    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered custom-modal">
            <div class="modal-content">
                <form>
                    <div class="text-center mb-3">
                        <img src="/assets/images/logo.png" alt="Logo" style="width: 80px;">
                    </div>

                    <div class="mb-3">
                        <input type="email" class="form-control" placeholder="Email" required>
                    </div>
                    <div class="mb-4">
                        <input type="password" class="form-control" placeholder="Password" required>
                    </div>

                    <button type="submit" class="btn btn-dark w-100 mb-2">Login</button>
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
                <form action="/pembeli" method="POST">
                    @csrf
                    <!-- Tombol Pilih -->
                    <div class="text-center mb-3">
                        <img src="/assets/images/logo.png" alt="Logo" style="width: 80px;">
                    </div>
                    <div class="d-flex justify-content-center mb-4">
                        <button type="button" class="btn btn-outline-success me-2"
                            onclick="showForm('customer')">Customer</button>
                        <button type="button" class="btn btn-success"
                            onclick="showForm('organization')">Organization</button>
                    </div>

                    <!-- Form Customer -->
                    <form id="form-customer" action="/pembeli" method="POST" style="display: none;">
                        @csrf
                        <input type="text" name="nama_pembeli" class="form-control mb-2" placeholder="Name"
                            required>
                        <input type="email" name="email_pembeli" class="form-control mb-2" placeholder="Email"
                            required>
                        <input type="date" name="tanggal_lahir" class="form-control mb-2" required>
                        <input type="tel" name="nomor_telepon" class="form-control mb-2"
                            placeholder="Phone Number" required>
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
                        <input type="text" name="alamat_organisasi" class="form-control mb-2"
                            placeholder="Address" required>
                        <input type="tel" name="nomor_telepon" class="form-control mb-2"
                            placeholder="Phone Number" required>
                        <input type="password" name="password" class="form-control mb-2" placeholder="Password"
                            required>
                        <input type="hidden" name="profil_pict" value="default.png">
                        <button type="submit" class="btn btn-success w-100 mb-2">Register as Organization</button>
                    </form>

                    <div class="text-center">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal"
                            data-bs-dismiss="modal">Login</a>
                    </div>

                </form>

            </div>
        </div>
    </div>

    @include('partials.footer')

    <script>
        function showForm(type) {
            document.getElementById('form-customer').style.display = type === 'customer' ? 'block' : 'none';
            document.getElementById('form-organization').style.display = type === 'organization' ? 'block' : 'none';
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
