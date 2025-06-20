<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFcmTokensTable extends Migration
{
    public function up()
    {
        Schema::create('fcm_tokens', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_pembeli')->unsigned()->nullable();
            $table->bigInteger('id_penitip')->unsigned()->nullable();
            $table->bigInteger('id_hunter')->unsigned()->nullable();
            $table->bigInteger('id_kurir')->unsigned()->nullable();
            $table->string('fcm_token')->unique();
            $table->string('device_type')->default('android');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('id_pembeli')->references('id_pembeli')->on('pembeli')->onDelete('cascade');
            $table->foreign('id_penitip')->references('id_penitip')->on('penitip')->onDelete('cascade');
            $table->foreign('id_hunter')->references('id_pegawai')->on('pegawai')->onDelete('cascade');
            $table->foreign('id_kurir')->references('id_pegawai')->on('pegawai')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('fcm_tokens');
    }
}