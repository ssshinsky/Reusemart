<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganisasisTable extends Migration
{
    public function up()
    {
        Schema::create('organisasi', function (Blueprint $table) {
            $table->id('id_organisasi');
            $table->string('nama_organisasi');
            $table->string('alamat');
            $table->string('kontak');
            $table->string('email_organisasi')->unique();
            $table->string('password');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('organisasi');
    }
};
