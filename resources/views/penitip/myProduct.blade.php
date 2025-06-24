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
                
                <div style="margin-bottom: 1rem;">
                    <input type="text" id="searchInput" class="form-control" placeholder="🔍 Search Your Products">
                </div>

                <div class="row" id="productGrid">
                    @include('penitip.partials.product_grid', ['products' => $products])
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const searchInput = document.getElementById('searchInput');
        let timeout = null;

        searchInput.addEventListener('input', function () {
            clearTimeout(timeout);
            const query = this.value;

            timeout = setTimeout(() => {
                fetch(`{{ route('penitip.products.search') }}?q=${encodeURIComponent(query)}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.text())
                .then(html => {
                    const grid = document.getElementById('productGrid');
                    grid.innerHTML = html;
                })
                .catch(err => console.error('Live search error:', err));
            }, 300);
        });

        function confirmPickup(id) {
            fetch(`/penitip/api/barang/${id}/check-pickup-info`)
                .then(res => res.json())
                .then(data => {
                    if (data.error) {
                        Swal.fire('Error', data.message, 'error');
                        return;
                    }

                    // 💬 Tentukan konten alert berdasarkan status_barang
                    let alertContent = '';

                    if (data.status_barang === 'Available') {
                        alertContent = `
                            <p>If you confirm this action, the item will no longer be available for sale and must be picked up within the next 7 days.</p>
                            <p><strong>Pickup Deadline:</strong> ${data.pickup_deadline}</p>
                            <p>If not picked up by the deadline, the item will be donated.</p>
                        `;
                    } else if (data.status_barang === 'Awaiting Owner Pickup') {
                        alertContent = `
                            <p>By confirming this, the item must be picked up within the remaining time.</p>
                            <p>It will no longer be listed for sale and must be collected before:</p>
                            <p><strong>📅 Pickup Deadline:</strong> ${data.pickup_deadline}</p>
                            <p>If not picked up by the deadline, the item will be donated.</p>
                        `;
                    }

                    Swal.fire({
                        title: 'Confirm Pickup',
                        html: alertContent,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Confirm Pickup',
                        confirmButtonColor: '#28a745',
                        cancelButtonText: 'Cancel',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(`/penitip/barang/${id}/confirm-pickup`, {
                                method: 'PATCH',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json'
                                }
                            })
                            .then(res => res.json())
                            .then(response => {
                                if (response.message) {
                                    Swal.fire('Success', response.message, 'success')
                                        .then(() => location.reload());
                                }
                            }).catch(() => {
                                Swal.fire('Error', 'Something went wrong', 'error');
                            });
                        }
                    });
                });
        }
    </script>
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