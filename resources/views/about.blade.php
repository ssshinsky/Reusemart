@extends('layouts.main')
@section('content')

    <body class="d-flex flex-column min-vh-100">
        <main class="flex-grow-1">
            <div class="container py-5">

                <div class="text-center mb-5">
                    <h2 class="fw-bold mb-4">Our Story</h2>
                    <div class="row align-items-center justify-content-center">
                        <div class="col-md-6 text-start">
                            <p>
                                Founded in 2025, Reusemart was born out of a simple yet powerful idea: to give new life to
                                pre-loved items and promote a more sustainable lifestyle. We believe that extending the life
                                of products is not just economical but also essential for our planet.
                            </p>
                            <p>
                                Our mission is to provide a seamless, user-friendly, and secure marketplace for buying,
                                selling, and donating secondhand goods. From electronics to fashion, furniture to
                                collectibles, every transaction on Reusemart contributes to a circular economy — one that
                                prioritizes reuse, reduces waste, and promotes conscious consumption.
                            </p>
                            <p>
                                We are driven by our vision: to create a thriving community where every item finds a
                                purpose, and nothing goes to waste.
                            </p>
                        </div>

                        <div class="col-md-6 pe-0">
                            <img src="/assets/images/about.jpg" alt="About Us Illustration"
                                class="img-fluid rounded-0 shadow-sm w-100">
                        </div>

                    </div>
                </div>

                <div class="text-center mb-5">
                    <h3 class="fw-bold mb-4">Meet Our Team</h3>
                    <div class="row justify-content-center">
                        <div class="col-md-3 text-center team-member mb-4">
                            <img src="/assets/images/team-russel.png" alt="Russel" class="team-photo">
                            <h5 class="mt-3">Russel</h5>
                            <p class="text-muted">Founder & Chairman</p>
                            <div class="social-icons">
                                <a href="#"><i class="bi bi-instagram"></i></a>
                                <a href="#"><i class="bi bi-twitter"></i></a>
                                <a href="#"><i class="bi bi-linkedin"></i></a>
                            </div>
                        </div>
                        <div class="col-md-3 text-center team-member mb-4">
                            <img src="{{ asset('images/team-gabriella.png') }}" alt="Gabriella" class="team-photo">
                            <h5 class="mt-3">Gabriella</h5>
                            <p class="text-muted">Programmer</p>
                            <div class="social-icons">
                                <a href="#"><i class="bi bi-instagram"></i></a>
                                <a href="#"><i class="bi bi-twitter"></i></a>
                                <a href="#"><i class="bi bi-linkedin"></i></a>
                            </div>
                        </div>
                        <div class="col-md-3 text-center team-member mb-4">
                            <img src="{{ asset('images/team-bernadeta.png') }}" alt="Bernadeta" class="team-photo">
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
    </body>
@endsection
