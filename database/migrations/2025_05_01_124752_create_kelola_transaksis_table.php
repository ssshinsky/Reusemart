<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKelolaTransaksisTable extends Migration
{
    public function up()
    {
        Schema::create('kelola_transaksi', function (Blueprint $table) {
            $table->id('id_kelola');
            $table->integer('id_pembelian');
            $table->foreign('id_pembelian')->references('id_pembelian')->on('transaksi_pembelian')->onDelete('cascade');

            $table->integer('id_pegawai');
            $table->foreign('id_pegawai')->references('id_pegawai')->on('pegawai')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('kelola_transaksi');
    }
};
