@extends('layouts.main')

@section('content')
    <div class="container py-5">
        <div class="row g-4">
            {{-- Checkout Summary --}}
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h4 class="mb-4 fw-bold">Review Pesanan</h4>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Gambar</th>
                                        <th>Barang</th>
                                        <th>Kuantitas</th>
                                        <th>Harga</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($items as $item)
                                        <tr>
                                            <td style="width: 100px;">
                                                @if ($item->barang->gambar->isNotEmpty())
                                                    <img src="{{ asset('storage/gambar/' . $item->barang->gambar->first()->gambar_barang) }}"
                                                        alt="{{ $item->barang->nama_barang }}" class="img-fluid rounded"
                                                        style="max-width: 80px;">
                                                @else
                                                    <img src="{{ asset('images/placeholder.jpg') }}" alt="No Image"
                                                        class="img-fluid rounded" style="max-width: 80px;">
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ $item->barang->nama_barang }}</strong><br>
                                                <small class="text-muted">{{ $item->barang->berat_barang }} Kg</small>
                                            </td>
                                            <td>1</td>
                                            <td>IDR {{ number_format($item->barang->harga_barang, 0, ',', '.') }}</td>
                                            <td>IDR {{ number_format($item->barang->harga_barang, 0, ',', '.') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">Tidak ada barang di pesanan.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    @if ($metodePengiriman === 'kurir' && $totalHarga < 1500000)
                                        <tr>
                                            <td colspan="4" class="text-end fw-bold">Ongkir</td>
                                            <td class="fw-bold">IDR {{ number_format(100000, 0, ',', '.') }}</td>
                                        </tr>
                                    @elseif ($metodePengiriman === 'kurir' && $totalHarga >= 1500000)
                                        <tr>
                                            <td colspan="4" class="text-end fw-bold">Ongkir</td>
                                            <td class="fw-bold">Free</td>
                                        </tr>
                                    @elseif ($metodePengiriman === 'ambil')
                                        <tr>
                                            <td colspan="4" class="text-end fw-bold">Ongkir</td>
                                            <td class="fw-bold">IDR {{ number_format(0, 0, ',', '.') }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td colspan="4" class="text-end fw-bold">Subtotal</td>
                                        <td class="fw-bold">IDR {{ number_format($totalHarga, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-end fw-bold">Total Setelah Poin</td>
                                        <td class="fw-bold" id="totalFinalDisplay">IDR
                                            {{ number_format($totalHarga, 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <h5 class="fw-bold">Informasi Pengiriman</h5>
                    <p>
                        <strong>Metode Pengiriman:</strong> {{ ucfirst($metodePengiriman) }} <br>
                        @if ($metodePengiriman === 'kurir')
                            <strong>Alamat:</strong> {{ $alamat ? $alamat->alamat_lengkap : '-' }}
                        @endif
                    </p>
                </div>

                <div class="alert alert-info mt-4 p-3">
                    <strong>Poin Dimiliki:</strong> {{ $poinDimiliki }} poin<br>
                    <small>Setiap pembelian Rp10.000 mendapatkan 1 Poin dan Bonus 20% poin untuk total di atas Rp500.000</small>
                </div>

                <div class="alert alert-warning text-center mt-4">
                    <p class="mb-2">Selesaikan pembayaran dalam waktu berikut sebelum transaksi dibatalkan:</p>
                    <div id="countdown" class="fs-4 fw-bold"></div>
                </div>
            </div>

            {{-- Payment Form --}}
            <div class="col-md-6">
                <div class="card shadow-sm border-0 p-4">
                    <h4 class="mb-4 fw-bold">Form Pembayaran</h4>
                    <form action="{{ route('pembeli.bayar') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="usePointsCheckbox">
                            <label class="form-check-label" for="usePointsCheckbox">Gunakan Poin</label>
                        </div>

                        <div id="poinExchangeSection" class="mb-3" style="display: none;">
                            <label for="poinDitukar" class="form-label">Jumlah Poin Ditukar</label>
                            <input type="number" class="form-control" id="poinDitukar" name="poin_ditukar" min="0"
                                max="{{ $poinDimiliki }}" value="0">
                        </div>

                        <div class="mb-3">
                            <label for="bukti_pembayaran" class="form-label">Upload Bukti Pembayaran</label>
                            <input class="form-control" type="file" id="bukti_pembayaran" name="bukti_pembayaran"
                                accept="image/*" required>
                        </div>

                        <input type="hidden" name="total_final" id="totalFinalInput"
                            value="{{ $totalHarga + ($totalHarga < 1500000 && $metodePengiriman === 'kurir' ? 100000 : 0) }}">
                        <input type="hidden" name="bonus_poin" id="bonusPoinInput" value="0">

                        <button type="submit" class="btn btn-primary w-100 rounded-pill">
                            <i class="fas fa-credit-card me-2"></i>Bayar Sekarang
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .card {
            border-radius: 10px;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        .countdown {
            font-family: monospace;
            color: #d9534f;
        }

        @media (max-width: 768px) {
            .table {
                font-size: 0.9rem;
            }

            .card-body {
                padding: 1rem;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Countdown
        function startCountdown(duration) {
            let timer = duration;
            const countdownElement = document.getElementById('countdown');

            const interval = setInterval(() => {
                let minutes = Math.floor(timer / 60);
                let seconds = timer % 60;

                minutes = minutes < 10 ? "0" + minutes : minutes;
                seconds = seconds < 10 ? "0" + seconds : seconds;

                countdownElement.textContent = `${minutes}:${seconds}`;

                if (--timer < 0) {
                    clearInterval(interval);
                    alert("Waktu habis! Transaksi dibatalkan.");
                    window.location.href =
                        "{{ route('pembeli.batalCheckout', ['id' => session('checkout_keranjang_id')]) }}";
                }
            }, 1000);
        }

        document.addEventListener('DOMContentLoaded', () => {
            startCountdown(60);

            const checkbox = document.getElementById('usePointsCheckbox');
            const poinSection = document.getElementById('poinExchangeSection');
            const poinInput = document.getElementById('poinDitukar');
            const bonusPoinInput = document.getElementById('bonusPoinInput');
            const poinDimiliki = parseInt({{ $poinDimiliki }});
            let totalHarga = {{ $totalHarga }};
            const ongkir = {{ $metodePengiriman === 'kurir' && $totalHarga < 1500000 ? 100000 : 0 }};
            // totalHarga += ongkir;
            const totalFinalInput = document.getElementById('totalFinalInput');
            const totalFinalDisplay = document.getElementById('totalFinalDisplay');

            checkbox.addEventListener('change', () => {
                poinSection.style.display = checkbox.checked ? 'block' : 'none';
                if (!checkbox.checked) {
                    poinInput.value = 0;
                    updateTotal(0);
                }
            });

            poinInput.addEventListener('input', () => {
                let poin = parseInt(poinInput.value) || 0;
                if (poin > poinDimiliki) {
                    poin = poinDimiliki;
                    poinInput.value = poin;
                }
                updateTotal(poin);
            });

            function updateTotal(poin) {
                let potongan = poin * 100;
                let finalTotal = totalHarga - potongan;
                if (finalTotal < 0) finalTotal = 0;

                // Update display total
                totalFinalDisplay.textContent = `IDR ${finalTotal.toLocaleString('id-ID')}`;
                totalFinalInput.value = finalTotal;

                // Hitung bonus poin
                let bonus = finalTotal > 500000 ? Math.floor((finalTotal / 10000) * 0.2) : 0;
                bonusPoinInput.value = bonus; // Set bonus poin ke input hidden
                document.getElementById('bonusPoinDisplay')?.remove(); // Clear old
                if (bonus > 0) {
                    const alert = document.createElement("div");
                    alert.classList.add("alert", "alert-success", "mt-3");
                    alert.id = "bonusPoinDisplay";
                    alert.innerHTML = `Kamu mendapatkan bonus <strong>${bonus} poin</strong>`;
                    document.getElementById('checkoutForm').appendChild(alert);
                }
            }

            updateTotal(0);
        });
    </script>
@endpush