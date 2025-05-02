<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePembelisTable extends Migration
{
    public function up()
    {
        Schema::create('pembeli', function (Blueprint $table) {
            $table->id('id_pembeli');
            $table->string('nama_pembeli');
            $table->string('email_pembeli')->unique();
            $table->date('tanggal_lahir');
            $table->string('nomor_telepon');
            $table->string('password');
            $table->integer('poin_pembeli')->default(0);
            $table->string('profil_pict')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pembeli');
    }
};