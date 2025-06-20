<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePenitipsTable extends Migration
{
    public function up()
    {
        Schema::create('penitip', function (Blueprint $table) {
            $table->id('id_penitip');
            $table->string('nik_penitip')->unique();
            $table->string('nama_penitip');
            $table->string('email_penitip')->unique();
            $table->string('password');
            $table->integer('poin_penitip')->nullable();
            $table->string('no_telp');
            $table->string('alamat');
            $table->float('rata_rating');
            $table->string('status_penitip');
            $table->double('saldo_penitip');
            $table->string('profil_pict')->nullable();
            $table->boolean('badge')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('penitip');
    }
};