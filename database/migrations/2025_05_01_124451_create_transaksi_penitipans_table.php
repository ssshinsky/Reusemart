<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaksiPenitipansTable extends Migration
{
    public function up()
    {
        Schema::create('transaksi_penitipan', function (Blueprint $table) {
            $table->id('id_transaksi_penitipan');
            $table->unsignedBigInteger('id_qc');
            $table->foreign('id_qc')->references('id_pegawai')->on('pegawai')->onDelete('cascade');

            $table->unsignedBigInteger('id_hunter')->nullable();
            $table->foreign('id_hunter')->references('id_pegawai')->on('pegawai')->onDelete('set null');

            $table->unsignedBigInteger('id_penitip');
            $table->foreign('id_penitip')->references('id_penitip')->on('penitip')->onDelete('cascade');

            $table->dateTime('tanggal_penitipan');
            $table->date('tanggal_berakhir');
            $table->integer('perpanjangan');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transaksi_penitipan');
    }
};