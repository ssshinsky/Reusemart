@extends('owner.owner_layout')

@section('title', 'Consignment Report')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark">Consignment Report</h2>
            <p class="text-muted">View consignors and download their previous month’s sales report</p>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th scope="col" style="width: 80px;">ID</th>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col" style="width: 180px;" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($penitips as $penitip)
                        <tr>
                            <td class="text-center">{{ $penitip->id_penitip }}</td>
                            <td>{{ $penitip->nama_penitip }}</td>
                            <td>{{ $penitip->email_penitip }}</td>
                            <td class="text-center">
                                <a href="{{ route('owner.download.consignment.pdf', ['id' => $penitip->id_penitip]) }}" 
                                    class="btn btn-outline-success btn-sm">
                                        <i class="bi bi-download me-1"></i> Download PDF
                                    </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                <i class="bi bi-info-circle me-2"></i>No consignors found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection