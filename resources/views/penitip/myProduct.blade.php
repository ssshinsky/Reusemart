@extends('layouts.main')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            {{-- Sidebar --}}
            <div class="col-md-3">
                @include('penitip.sidebar')
            </div>

            {{-- My Products --}}
            <div class="col-md-9">
                <div class="mb-4 border-bottom pb-2">
                    <h4 class="fw-bold text-success">My Products</h4>
                    <p class="text-muted">List of products you've consigned.</p>
                </div>

                <div class="row">
                    @forelse ($products as $product)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card h-100 shadow-sm border-0 hover-shadow">
                                <img src="{{ asset('storage/' . ($product->gambar->first()->nama_file ?? 'default.jpg')) }}"
                                    class="card-img-top rounded-top" style="height: 220px; object-fit: cover;"
                                    alt="{{ $product->nama_barang }}">
                                <div class="card-body">
                                    <h5 class="card-title text-truncate">{{ $product->nama_barang }}</h5>
                                    <p class="mb-0">
                                        Status:
                                        @php
                                            $status = [
                                                'AVAILABLE' => ['label' => 'Available', 'class' => 'success'],
                                                'SOLD OUT' => ['label' => 'Sold Out', 'class' => 'danger'],
                                                'DONATED' => ['label' => 'Donated', 'class' => 'warning text-dark'],
                                                'COLLECTED' => ['label' => 'Collected', 'class' => 'primary'],
                                            ];
                                            $badge = $status[$product->status_barang] ?? [
                                                'label' => 'Unknown',
                                                'class' => 'secondary',
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $badge['class'] }}">{{ $badge['label'] }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info text-center">
                                Belum ada produk tersedia.
                            </div>
                        </div>
                    @endforelse
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
    </style>
@endpush
