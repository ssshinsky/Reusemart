<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaksiPembeliansTable extends Migration
{
    public function up()
    {
        Schema::create('transaksi_pembelian', function (Blueprint $table) {
            $table->id('id_pembelian');
            $table->integer('id_keranjang');
            $table->foreign('id_keranjang')->references('id_keranjang')->on('keranjang')->onDelete('cascade');
            $table->integer('id_alamat')->nullable(); // Ubah menjadi nullable
            $table->foreign('id_alamat')->references('id_alamat')->on('alamat')->onDelete('set null');
            $table->string('no_resi');
            $table->dateTime('tanggal_pembelian');
            $table->dateTime('waktu_pembayaran')->nullable();
            $table->string('bukti_tf')->nullable();
            $table->double('total_harga_barang')->nullable();
            $table->string('metode_pengiriman');
            $table->double('ongkir');
            $table->dateTime('tanggal_ambil')->nullable();
            $table->dateTime('tanggal_pengiriman')->nullable();
            $table->double('total_harga')->nullable();
            $table->string('status_transaksi')->default('Menunggu Pembayaran');
            $table->integer('poin_terpakai')->nullable();
            $table->integer('poin_pembeli')->nullable();
            $table->integer('poin_penitip')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transaksi_pembelian');
    }
}