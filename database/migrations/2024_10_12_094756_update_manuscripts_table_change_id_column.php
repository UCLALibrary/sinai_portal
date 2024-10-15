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
        Schema::table('manuscripts', function (Blueprint $table) {
            $table->string('id', 255)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('manuscripts', function (Blueprint $table) {
            DB::statement('ALTER TABLE manuscripts ALTER COLUMN id TYPE bigint USING id::bigint');
        });
    }
};
