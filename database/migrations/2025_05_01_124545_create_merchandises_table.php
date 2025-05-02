<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchandisesTable extends Migration
{
    public function up()
    {
        Schema::create('merchandise', function (Blueprint $table) {
            $table->id('id_merchandise');
            $table->unsignedBigInteger('id_pegawai');
            $table->foreign('id_pegawai')->references('id_pegawai')->on('pegawai')->onDelete('cascade');

            $table->string('nama_merch');
            $table->integer('poin');
            $table->integer('stok');
            $table->string('gambar_merch');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('merchandise');
    }
};
