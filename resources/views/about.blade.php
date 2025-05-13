<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>About Us - Reusemart</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Fonts and Icons --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .team-member img {
            width: 100%;
            max-height: 220px;
            object-fit: cover;
            border-radius: 12px;
        }

        .social-icons a {
            margin: 0 5px;
            color: #333;
            font-size: 1.2rem;
        }

        .social-icons a:hover {
            color: #000;
        }

        footer {
            background-color: #000;
            color: #fff;
            text-align: center;
            padding: 1.5rem 0;
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">

    @include('partials.navbar')

    <main class="flex-grow-1">
        <div class="container py-5">

            <div class="text-center mb-5">
                <h2 class="fw-bold">Our Story</h2>
                <div class="row justify-content-center mt-4">
                    <div class="col-md-8">
                        <p>
                            Founded in 2025, Reusemart was born out of a simple yet powerful idea: to give new life to
                            pre-loved
                            items and promote a more sustainable lifestyle. We believe that extending the life of
                            products is
                            not just economical but also essential for our planet.
                        </p>
                        <p>
                            Our mission is to provide a seamless, user-friendly, and secure marketplace for buying,
                            selling, and
                            donating secondhand goods. From electronics to fashion, furniture to collectibles, every
                            transaction
                            on Reusemart contributes to a circular economy — one that prioritizes reuse, reduces waste,
                            and
                            promotes conscious consumption.
                        </p>
                        <p>
                            We are driven by our vision: to create a thriving community where every item finds a
                            purpose, and
                            nothing goes to waste.
                        </p>
                    </div>
                </div>
                <img src="{{ asset('images/about-illustration.png') }}" alt="About Us Illustration"
                    class="img-fluid my-4" style="max-height: 300px;">
            </div>

            <div class="text-center mb-5">
                <h3 class="fw-bold mb-4">Meet Our Team</h3>
                <div class="row justify-content-center">
                    <div class="col-md-3 text-center team-member mb-4">
                        <img src="{{ asset('images/team-russel.png') }}" alt="Russel">
                        <h5 class="mt-3">Russel</h5>
                        <p class="text-muted">Founder & Chairman</p>
                        <div class="social-icons">
                            <a href="#"><i class="bi bi-instagram"></i></a>
                            <a href="#"><i class="bi bi-twitter"></i></a>
                            <a href="#"><i class="bi bi-linkedin"></i></a>
                        </div>
                    </div>
                    <div class="col-md-3 text-center team-member mb-4">
                        <img src="{{ asset('images/team-gabriella.png') }}" alt="Gabriella">
                        <h5 class="mt-3">Gabriella</h5>
                        <p class="text-muted">Managing Director</p>
                        <div class="social-icons">
                            <a href="#"><i class="bi bi-instagram"></i></a>
                            <a href="#"><i class="bi bi-twitter"></i></a>
                            <a href="#"><i class="bi bi-linkedin"></i></a>
                        </div>
                    </div>
                    <div class="col-md-3 text-center team-member mb-4">
                        <img src="{{ asset('images/team-bernadeta.png') }}" alt="Bernadeta">
                        <h5 class="mt-3">Bernadeta</h5>
                        <p class="text-muted">Product Designer</p>
                        <div class="social-icons">
                            <a href="#"><i class="bi bi-instagram"></i></a>
                            <a href="#"><i class="bi bi-twitter"></i></a>
                            <a href="#"><i class="bi bi-linkedin"></i></a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

    @include('partials.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
