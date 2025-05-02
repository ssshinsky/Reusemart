<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlamatsTable extends Migration
{
    public function up()
    {
        Schema::create('alamat', function (Blueprint $table) {
            $table->id('id_alamat');
            $table->unsignedBigInteger('id_pembeli');
            $table->string('nama_orang')->nullable();
            $table->string('label_alamat');
            $table->string('alamat_lengkap');
            $table->string('nomor_telepon');
            $table->string('kode_pos');
            $table->boolean('is_default');
            $table->timestamps();

            $table->foreign('id_pembeli')->references('id_pembeli')->on('pembeli')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('alamat');
    }
};