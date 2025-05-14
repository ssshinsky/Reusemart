@extends('owner.owner_layout')

@section('title', 'Poin Reward')

@section('content')
  <h2 class="text-2xl font-bold mb-4">Poin Reward</h2>
  <div class="table-container">
    <div class="table-scroll-x">
      <table class="custom-table">
        <thead>
          <tr>
            <th style="width: 200px;">Penitip</th>
            <th style="width: 200px;">Barang</th>
            <th style="width: 100px;">Poin</th>
            <th style="width: 150px;">Nilai (Rp)</th>
          </tr>
        </thead>
        <tbody id="rewardsTableBody">
          <tr>
            <td colspan="4" class="center">Loading...</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      fetch('http://localhost/api/v1/donasi')
        .then(response => response.json())
        .then(data => {
          const tableBody = document.getElementById('rewardsTableBody');
          tableBody.innerHTML = '';
          if (data.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="4" class="center">Belum ada poin reward.</td></tr>';
            return;
          }
          data.forEach(item => {
            const penitip = item.barang?.transaksi_penitipan?.penitip?.nama_penitip || 'Unknown';
            const poin = (item.barang?.harga_barang || 0) / 10000;
            const row = document.createElement('tr');
            row.innerHTML = `
              <td>${penitip}</td>
              <td>${item.barang?.nama_barang || 'N/A'}</td>
              <td class="center">${poin}</td>
              <td class="center">${(poin * 10000).toLocaleString('id-ID')}</td>
            `;
            tableBody.appendChild(row);
          });
        })
        .catch(error => {
          console.error('Error:', error);
          document.getElementById('rewardsTableBody').innerHTML = '<tr><td colspan="4" class="center text-red-500">Error loading data</td></tr>';
        });
    });
  </script>
@endpush