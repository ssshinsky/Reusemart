<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('transaksi_pembelian', function (Blueprint $table) {
            $table->unsignedBigInteger('id_kurir')->nullable()->after('id_pembeli'); // Menambahkan kolom id_kurir
            $table->foreign('id_kurir')->references('id_pegawai')->on('pegawai')->onDelete('set null'); // Relasi ke tabel pegawai
        });
    }

    public function down()
    {
        Schema::table('transaksi_pembelian', function (Blueprint $table) {
            $table->dropForeign(['id_kurir']);
            $table->dropColumn('id_kurir');
        });
    }
};
