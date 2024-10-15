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
        Schema::table('works', function (Blueprint $table) {
            $table->dropPrimary('works_pkey');
        });

        Schema::table('works', function (Blueprint $table) {
            $table->string('id', 255)->change();
        });

        Schema::table('works', function (Blueprint $table) {
            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('works', function (Blueprint $table) {
            $table->dropPrimary('works_pkey');
        });

        Schema::table('works', function (Blueprint $table) {
            DB::statement('ALTER TABLE works ALTER COLUMN id TYPE bigint USING id::bigint');
        });

        Schema::table('works', function (Blueprint $table) {
            $table->primary('id');
        });
    }
};
