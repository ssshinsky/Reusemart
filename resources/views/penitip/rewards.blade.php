@extends('layouts.main')

@section('content')
    <div class="container py-4">
        <div class="row">
            <div class="col-md-3">
                @include('penitip.sidebar')
            </div>
            <div class="col-md-9">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="text-success fw-bold mb-3 text-center">Income and Reward Details</h4>

                        <div class="row g-4 justify-content-center">
                            <div class="col-md-6">
                                <div class="border border-success rounded p-4 text-center">
                                    <h5 class="text-muted mb-2">Your Balance</h5>
                                    <p class="fs-3 fw-bold text-success">
                                        Rp{{ number_format($penitip->saldo_penitip, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="border border-warning rounded p-4 text-center">
                                    <h5 class="text-muted mb-2">Your Points</h5>
                                    <p class="fs-3 fw-bold text-warning">
                                        {{ $penitip->poin_penitip }} Points
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #00b14f;
            --text-dark: #212529;
            --text-muted: #6c757d;
            --bg-light: #f8f9fa;
            --border-color: #dee2e6;
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

        body {
            font-family: 'Poppins', sans-serif;
        }

        .text-success {
            color: var(--primary-color) !important;
        }

        .btn-success {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-success:hover {
            background-color: #019944;
            border-color: #019944;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            font-weight: 500;
            border: none;
            border-left: 4px solid transparent;
            color: #333;
            transition: all 0.3s;
        }

        .sidebar-menu a.active {
            background-color: var(--bg-light);
            border-left: 4px solid var(--primary-color);
            color: var(--primary-color);
        }

        .sidebar-menu a:hover {
            background-color: #f1f1f1;
            color: var(--primary-color);
        }

        .sidebar-menu i {
            margin-right: 10px;
        }

        .card {
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 24px;
        }
    </style>
@endpush
