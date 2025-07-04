<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    public function up()
    {
        Schema::create('role', function (Blueprint $table) {
            $table->id('id_role');
            $table->string('nama_role');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('role');
    }
};
