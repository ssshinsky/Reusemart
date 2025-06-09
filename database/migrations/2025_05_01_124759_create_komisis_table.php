<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKomisisTable extends Migration
{
    public function up()
    {
        Schema::create('komisi', function (Blueprint $table) {
            $table->id('id_komisi');
            $table->integer('id_pembelian')->nullable();
            $table->foreign('id_pembelian')->references('id_pembelian')->on('transaksi_pembelian')->onDelete('cascade');

            $table->integer('id_penitip')->nullable();
            $table->foreign('id_penitip')->references('id_penitip')->on('penitip')->onDelete('cascade');

            $table->integer('id_hunter')->nullable();
            $table->foreign('id_hunter')->references('id_pegawai')->on('pegawai')->onDelete('set null');

            $table->integer('id_owner')->nullable();
            $table->foreign('id_owner')->references('id_pegawai')->on('pegawai')->onDelete('set null');

            $table->double('komisi_hunter')->nullable();
            $table->double('komisi_penitip')->nullable();
            $table->double('komisi_reusemart')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('komisi');
    }
};
