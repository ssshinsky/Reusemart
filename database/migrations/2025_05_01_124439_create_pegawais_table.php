<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePegawaisTable extends Migration
{
    public function up()
    {
        Schema::create('pegawai', function (Blueprint $table) {
            $table->id('id_pegawai');
            $table->unsignedBigInteger('id_role');
            $table->foreign('id_role')->references('id_role')->on('role')->onDelete('cascade');
            $table->string('nama_pegawai');
            $table->string('alamat_pegawai');
            $table->date('tanggal_lahir');
            $table->string('nomor_telepon');
            $table->double('gaji_pegawai');
            $table->string('email_pegawai')->unique();
            $table->string('password');
            $table->integer('profil_pict')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pegawai');
    }
};
