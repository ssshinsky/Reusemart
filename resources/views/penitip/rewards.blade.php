@extends('layouts.penitip')

@section('content')
    <div class="container mx-auto p-6">
        <h2 class="text-xl font-semibold mb-4 text-center">Income and Reward Details</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 justify-center items-center">
            <div class="bg-white shadow rounded-lg p-6 border border-green-500 text-center">
                <h3 class="text-lg font-medium mb-2">Your Balance</h3>
                <p class="text-3xl font-bold text-green-700">
                    Rp{{ number_format($penitip->saldo_penitip, 0, ',', '.') }}
                </p>
            </div>

            <div class="bg-white shadow rounded-lg p-6 border border-yellow-500 text-center">
                <h3 class="text-lg font-medium mb-2">Your Points</h3>
                <p class="text-3xl font-bold text-yellow-600">
                    {{ $penitip->poin_penitip }} Points
                </p>
            </div>
        </div>
    </div>
@endsection
