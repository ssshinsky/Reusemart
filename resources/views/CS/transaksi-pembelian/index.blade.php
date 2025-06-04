@extends('CS.dashboard')

@section('title', 'Purchase Transaction Confirmation')

@section('content')
    <h2 style="font-size: 1.5rem; margin-bottom: 1rem;"><strong>Purchase Transaction Confirmation</strong></h2>

    <div style="margin: 0.75rem 0; display: flex; gap: 0.5rem;">
        <button class="btn-action" id="verifyToggle">✅ Verify Transaction</button>
    </div>

    <!-- <div style="margin-bottom: 0.75rem; display: flex; gap: 0.5rem; align-items: center;">
        <input type="text" id="searchInput" placeholder="🔍 Search by Receipt No. or Username" class="input-search">
    </div> -->

    <div class="table-container">
        <div class="table-scroll-x">
            <table class="table-penitip">
                <thead>
                    <tr>
                        <th class="col-resi">Receipt No.</th>
                        <th class="col-username">Username</th>
                        <th class="col-waktu">Payment Time</th>
                        <th class="col-bukti">Transfer Proof</th>
                        <th class="col-metode">Shipping Method</th>
                        <th class="col-status">Status</th>
                        <th class="col-detail">Detail</th>
                        <th class="col-action sticky-action header-action"
                            style="display: none; background-color: #ffce53;">Action</th>
                    </tr>
                </thead>
                <tbody id="transaksiTableBody">
                    @foreach ($transaksi as $item)
                        <tr>
                            <td>{{ $item->no_resi }}</td>
                            <td>
                                {{ $item->keranjang?->detailKeranjang?->first()?->itemKeranjang?->pembeli?->nama_pembeli ?? 'Unknown' }}
                            </td>
                            <td>
                                {{ $item->waktu_pembayaran ? $item->waktu_pembayaran->format('d M Y H:i') : '-' }}
                            </td>
                            <td>
                                @if ($item->bukti_tf)
                                    <a href="{{ asset('storage/' . $item->bukti_tf) }}" target="_blank">View Proof</a>
                                @else
                                    No Proof
                                @endif
                            </td>
                            <td>{{ ucfirst($item->metode_pengiriman) }}</td>
                            <td>{{ $item->status_transaksi }}</td>
                            <td class="center">
                                <button class="btn-detail" data-bs-toggle="modal"
                                    data-bs-target="#detailModal{{ $item->id_pembelian }}">📋</button>
                            </td>
                            <td class="action-cell" style="background-color: rgb(255, 245, 220)">
                                @if ($item->status_transaksi === 'Menunggu Konfirmasi')
                                    <form
                                        action="{{ route('cs.transaksi-pembelian.verify', ['id_pembelian' => $item->id_pembelian]) }}"
                                        method="POST" class="form-verify" style="display:inline;">
                                        @csrf
                                        <input type="hidden" name="is_valid" value="1">
                                        <button type="submit" class="verify-btn">✅</button>
                                    </form>
                                    <form
                                        action="{{ route('cs.transaksi-pembelian.verify', ['id_pembelian' => $item->id_pembelian]) }}"
                                        method="POST" class="form-verify" style="display:inline;">
                                        @csrf
                                        <input type="hidden" name="is_valid" value="0">
                                        <button type="submit" class="reject-btn">❌</button>
                                    </form>
                                @else
                                    <span>-</span>
                                @endif
                            </td>
                        </tr>
                        <!-- Modal Detail Pembelian -->
                        <div class="modal fade" id="detailModal{{ $item->id_pembelian }}" tabindex="-1"
                            aria-labelledby="detailModalLabel{{ $item->id_pembelian }}" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header bg-success text-white">
                                        <h5 class="modal-title" id="detailModalLabel{{ $item->id_pembelian }}">📋 Detail
                                            Pembelian - {{ $item->no_resi }}</h5>
                                        <button type="button" class="btn-close bg-white" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>Username:</strong>
                                            {{ $item->keranjang?->detailKeranjang?->first()?->itemKeranjang?->pembeli?->nama_pembeli ?? 'Unknown' }}
                                        </p>
                                        <p><strong>Waktu Pembayaran:</strong>
                                            {{ $item->waktu_pembayaran ? $item->waktu_pembayaran->format('d M Y H:i') : '-' }}
                                        </p>
                                        <p><strong>Metode Pengiriman:</strong> {{ ucfirst($item->metode_pengiriman) }}</p>
                                        <p><strong>Status Transaksi:</strong> {{ $item->status_transaksi }}</p>
                                        <hr>
                                        <h6>📦 Rincian Produk:</h6>
                                        <ul class="list-group">
                                            @foreach ($item->keranjang?->detailKeranjang ?? [] as $detail)
                                                <li
                                                    class="list-group-item d-flex justify-content-between align-items-center">
                                                    {{ $detail->itemKeranjang?->nama_barang ?? 'Produk tidak ditemukan' }}
                                                    <span class="badge bg-primary rounded-pill">{{ $detail->jumlah }} x
                                                        Rp{{ number_format($detail->harga_satuan, 0, ',', '.') }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                        <hr>
                                        @if ($item->bukti_tf)
                                            <p><strong>Bukti Transfer:</strong></p>
                                            <img src="{{ asset('storage/' . $item->bukti_tf) }}" alt="Bukti Transfer"
                                                class="img-fluid rounded border">
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function rebindVerifyToggle() {
            const actionCells = document.querySelectorAll('.action-cell');
            const headerAction = document.querySelector('.header-action');
            const toggleBtn = document.getElementById('verifyToggle');
            const isActive = toggleBtn?.classList.contains('active');

            if (isActive) {
                headerAction?.style && (headerAction.style.display = 'table-cell');
                actionCells.forEach(cell => cell.classList.add('visible'));
            } else {
                headerAction?.style && (headerAction.style.display = 'none');
                actionCells.forEach(cell => cell.classList.remove('visible'));
            }
        }

        const toggleButton = document.getElementById('verifyToggle');
        toggleButton.addEventListener('click', function() {
            toggleButton.classList.toggle('active');
            rebindVerifyToggle();
        });

        const searchInput = document.getElementById('searchInput');
        let timeout = null;

        searchInput.addEventListener('input', function() {
            clearTimeout(timeout);
            const query = this.value;

            timeout = setTimeout(() => {
                fetch(`{{ route('cs.transaksi-pembelian.search') }}?q=${encodeURIComponent(query)}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.text())
                    .then(html => {
                        const tbody = document.getElementById('transaksiTableBody');
                        if (tbody) {
                            tbody.innerHTML = html;
                            rebindVerifyToggle();
                        }
                    })
                    .catch(err => console.error('Live search error:', err));
            }, 300);
        });

        document.querySelectorAll('.form-verify').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const isValid = form.querySelector('input[name="is_valid"]').value === '1';
                Swal.fire({
                    title: isValid ? 'Verify Transaction?' : 'Reject Transaction?',
                    text: isValid ?
                        'The payment proof will be validated, and the status will be set to Prepared.' :
                        'The payment proof will be rejected, and the transaction will be canceled.',
                    icon: isValid ? 'question' : 'warning',
                    showCancelButton: true,
                    confirmButtonColor: isValid ? '#28a745' : '#d33',
                    cancelButtonColor: '#aaa',
                    confirmButtonText: isValid ? 'Yes, Validate!' : 'Yes, Reject!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>

    <style>
        .input-search {
            flex: 1;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        .table-container {
            margin-top: 0.75rem;
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 4px;
            overflow-x: auto;
            max-height: 400px;
        }

        .table-penitip {
            width: max-content;
            min-width: 100%;
            border-collapse: collapse;
            background-color: #F9FAFB;
            font-size: 14px;
        }

        .table-penitip th,
        .table-penitip td {
            padding: 8px 12px;
            height: 40px;
            border: 1px solid #ccc;
            white-space: nowrap;
        }

        .table-penitip th {
            background-color: #A7F3D0;
            text-align: center;
            font-weight: 600;
        }

        .center {
            text-align: center;
        }

        .col-resi {
            width: 100px;
        }

        .col-username {
            width: 160px;
        }

        .col-waktu {
            width: 130px;
        }

        .col-bukti {
            width: 120px;
        }

        .col-metode {
            width: 100px;
        }

        .col-status {
            width: 100px;
        }

        .col-detail {
            width: 60px;
        }

        .col-action {
            width: 100px;
            position: sticky;
            right: 0;
            background: #fff;
            z-index: 1;
        }

        .sticky-action {
            position: sticky;
            right: 0;
        }

        .action-cell {
            position: sticky;
            right: 0;
            background: #fff;
            display: none;
            justify-content: center;
            gap: 6px;
        }

        .action-cell.visible {
            display: flex;
            border: none;
        }

        .verify-btn,
        .reject-btn,
        .btn-detail {
            border: none;
            background: none;
            cursor: pointer;
            font-size: 16px;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
        }

        .verify-btn {
            background-color: #28a745;
        }

        .reject-btn {
            background-color: #dc3545;
        }

        .btn-detail {
            background-color: #17a2b8;
        }

        .btn-action {
            padding: 8px 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background: white;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-action.active {
            background-color: #DCDCDC;
        }

        .btn-action:hover {
            background-color: #EAEAEA;
        }

        .table-penitip a {
            color: #007bff;
            text-decoration: none;
        }

        .table-penitip a:hover {
            text-decoration: underline;
        }
    </style>
@endsection
