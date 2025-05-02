<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestDonasisTable extends Migration
{
    public function up()
    {
        Schema::create('request_donasi', function (Blueprint $table) {
            $table->id('id_request');
            $table->unsignedBigInteger('id_organisasi');
            $table->foreign('id_organisasi')->references('id_organisasi')->on('organisasi')->onDelete('cascade');

            $table->unsignedBigInteger('id_pegawai');
            $table->foreign('id_pegawai')->references('id_pegawai')->on('pegawai')->onDelete('cascade');

            $table->string('request');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('request_donasi');
    }
};
