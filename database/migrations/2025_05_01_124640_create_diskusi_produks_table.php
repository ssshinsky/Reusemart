<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiskusiProduksTable extends Migration
{
    public function up()
    {
        Schema::create('diskusi_produk', function (Blueprint $table) {
            $table->id('id_diskusi_produk');
            $table->unsignedBigInteger('id_barang');
            $table->foreign('id_barang')->references('id_barang')->on('barang')->onDelete('cascade');
            
            $table->unsignedBigInteger('id_pembeli')->nullable();
            $table->foreign('id_pembeli')->references('id_pembeli')->on('pembeli')->onDelete('cascade');
            
            $table->unsignedBigInteger('id_pegawai')->nullable();
            $table->foreign('id_pegawai')->references('id_pegawai')->on('pegawai')->onDelete('cascade');
            $table->string('diskusi');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('diskusi_produk');
    }
};