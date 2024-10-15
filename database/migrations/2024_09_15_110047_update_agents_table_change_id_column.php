<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            DB::statement('ALTER TABLE agents ALTER COLUMN id TYPE bigint USING id::bigint');
        });

        Schema::table('agents', function (Blueprint $table) {
            $table->primary('id');
        });
    }
};
