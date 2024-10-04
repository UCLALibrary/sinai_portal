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
        Schema::table('agents', function (Blueprint $table) {
            $table->dropPrimary('agents_pkey');
        });

        Schema::table('agents', function (Blueprint $table) {
            $table->string('id', 255)->change();
        });

        Schema::table('agents', function (Blueprint $table) {
            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->dropPrimary('agents_pkey');
        });

        Schema::table('agents', function (Blueprint $table) {
            $table->bigInteger('id')->change();
        });

        Schema::table('agents', function (Blueprint $table) {
            $table->primary('id');
        });
    }
};
