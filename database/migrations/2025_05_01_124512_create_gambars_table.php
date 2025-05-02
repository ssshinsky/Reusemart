<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGambarsTable extends Migration
{
    public function up()
    {
        Schema::create('gambar', function (Blueprint $table) {
            $table->id('id_gambar');
            $table->unsignedBigInteger('id_barang');
            $table->foreign('id_barang')->references('id_barang')->on('barang')->onDelete('cascade');

            $table->string('gambar_barang');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('gambar');
    }
};