<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaksiMerchandisesTable extends Migration
{
    public function up()
    {
        Schema::create('transaksi_merchandise', function (Blueprint $table) {
            $table->id('id_transaksi_merchandise');
            $table->unsignedBigInteger('id_merchandise');
            $table->foreign('id_merchandise')->references('id_merchandise')->on('merchandise')->onDelete('cascade');

            $table->unsignedBigInteger('id_pembeli');
            $table->foreign('id_pembeli')->references('id_pembeli')->on('pembeli')->onDelete('cascade');
            $table->integer('jumlah');
            $table->integer('total_poin_penukaran');
            $table->date('tanggal_klaim')->nullable();
            $table->date('tanggal_ambil_merch')->nullable();
            $table->string('status_transaksi')->default('belum di ambil');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transaksi_merchandise');
    }
}