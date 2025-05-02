<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemKeranjangsTable extends Migration
{
    public function up()
    {
        Schema::create('item_keranjang', function (Blueprint $table) {
            $table->id('id_item_keranjang');
            $table->unsignedBigInteger('id_pembeli');
            $table->foreign('id_pembeli')->references('id_pembeli')->on('pembeli')->onDelete('cascade');

            $table->unsignedBigInteger('id_barang');
            $table->foreign('id_barang')->references('id_barang')->on('barang')->onDelete('cascade');
            $table->boolean('is_selected');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('item_keranjang');
    }
};