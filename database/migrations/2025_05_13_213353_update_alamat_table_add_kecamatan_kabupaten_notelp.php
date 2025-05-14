<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::table('alamat', function (Blueprint $table) {
        $table->string('kecamatan')->nullable()->after('alamat_lengkap');
        $table->string('kabupaten')->nullable()->after('kecamatan');
        $table->string('no_telepon')->nullable()->after('kabupaten'); // ubah dari `nomor_telepon` ke `no_telepon`
        $table->dropColumn('nomor_telepon'); // jika sebelumnya sudah ada
    });
}



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
