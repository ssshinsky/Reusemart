<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBarangsTable extends Migration
{
    public function up()
    {
        Schema::create('barang', function (Blueprint $table) {
            $table->id('id_barang');
            $table->unsignedBigInteger('id_kategori');
            $table->foreign('id_kategori')->references('id_kategori')->on('kategori')->onDelete('cascade');
            $table->unsignedBigInteger('id_transaksi_penitipan');
            $table->foreign('id_transaksi_penitipan')->references('id_transaksi_penitipan')->on('transaksi_penitipan')->onDelete('cascade');
            $table->string('kode_barang', 10);
            $table->string('nama_barang');
            $table->double('harga_barang');
            $table->float('berat_barang');
            $table->string('deskripsi_barang');
            $table->string('status_garansi');
            $table->string('status_barang');
            $table->date('tanggal_garansi')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('barang');
    }
};