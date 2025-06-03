<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReuseMart</title>

    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- di dalam <head> -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flipdown@0.3.2/dist/flipdown.min.css">
    <!-- sebelum </body> -->
    <script src="https://cdn.jsdelivr.net/npm/flipdown@0.3.2/dist/flipdown.min.js"></script>

    {{-- Google Fonts - Poppins --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    {{-- Custom CSS (opsional) --}}
    <style>
        :root {
            --primary-color: #00b14f;
            --text-dark: #212529;
            --text-muted: #6c757d;
            --bg-light: #f8f9fa;
            --border-color: #dee2e6;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            font-size: 0.875rem;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
        }

        .flipdown {
            margin: 0 auto;
        }

        .flipdown .rotor,
        .flipdown .rotor-leaf {
            background: #fff;
            color: #000;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 1.5rem 1rem;
            flex: 1 0 auto;
        }

        .text-primary {
            color: var(--primary-color) !important;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #019944;
            border-color: #019944;
            transform: scale(1.03);
        }

        .btn-outline {
            background-color: transparent;
            border: 1px solid var(--border-color);
            border-radius: 50%;
            padding: 0.4rem;
            transition: all 0.3s ease;
        }

        .btn-outline:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        .card {
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1rem;
            background-color: white;
            transition: box-shadow 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.08);
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-bottom: 1rem;
        }

        .breadcrumb a {
            color: var(--text-dark);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .breadcrumb a:hover {
            color: var(--primary-color);
        }

        .slider {
            position: relative;
            overflow: hidden;
            width: 100%;
            max-height: 300px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
        }

        .slider-container {
            display: flex;
            transition: transform 0.5s ease-in-out;
            width: 100%;
        }

        .slide {
            min-width: 100%;
            height: 300px;
            object-fit: contain;
            border-radius: 12px;
        }

        .slider-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            padding: 0.5rem;
            cursor: pointer;
            z-index: 10;
            font-size: 0.75rem;
            transition: background-color 0.3s ease;
        }

        .slider-btn:hover {
            background-color: var(--primary-color);
        }

        .slider-btn.prev {
            left: 0;
            border-radius: 0 6px 6px 0;
        }

        .slider-btn.next {
            right: 0;
            border-radius: 6px 0 0 6px;
        }

        .dots {
            position: absolute;
            bottom: 0.5rem;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 0.4rem;
        }

        .dot {
            width: 8px;
            height: 8px;
            background-color: #d1d5db;
            border-radius: 50%;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .dot.active {
            background-color: var(--primary-color);
        }

        .product-info {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .product-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-dark);
        }

        .product-price {
            font-size: 1.125rem;
            font-weight: 600;
            color: #dc2626;
        }

        .product-description {
            font-size: 0.875rem;
            color: var(--text-muted);
            line-height: 1.5;
        }

        .warranty-box {
            background-color: var(--bg-light);
            padding: 0.75rem;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            font-size: 0.75rem;
        }

        .discussion-section {
            margin-top: 1.5rem;
        }

        .discussion-form textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            resize: vertical;
            font-size: 0.875rem;
            color: var(--text-dark);
            transition: border-color 0.3s ease;
        }

        .discussion-form textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(0, 177, 79, 0.1);
        }

        .discussion-item {
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background-color: var(--bg-light);
            transition: box-shadow 0.3s ease;
        }

        .discussion-item:hover {
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.05);
        }

        .discussion-item.admin {
            margin-left: 1rem;
            background-color: #e6f0ff;
            border-color: #bfdbfe;
        }

        @media (max-width: 768px) {
            .grid-cols-3 {
                grid-template-columns: 1fr;
            }

            .slider {
                max-height: 200px;
            }

            .slide {
                height: 200px;
            }

            .product-title {
                font-size: 1rem;
            }

            .product-price {
                font-size: 1rem;
            }

            .btn-primary {
                padding: 0.4rem 0.8rem;
                font-size: 0.75rem;
            }
        }

        /* body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
        } */

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

        <style>.team-photo {
            height: 280px;
            width: 100%;
            object-fit: cover;
            border-radius: 12px;
        }
    </style>

    @stack('styles')
</head>

<body>

    {{-- Navbar --}}
    @include('partials.navbar')

    {{-- Konten Utama --}}
    <div class="container my-4">
        @yield('content')
    </div>

    {{-- Footer --}}
    @include('partials.footer')

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')

</body>

</html>
