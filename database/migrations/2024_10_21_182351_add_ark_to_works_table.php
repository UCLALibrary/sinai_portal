<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::transaction(function () {
            // create a new table with the correct column order
            Schema::create('works_temp', function (Blueprint $table) {
                $table->string('id', 255)->primary();
                $table->string('ark');
                $table->string('pref_title');
                $table->jsonb('json');
                $table->timestamps();
            });

            // copy data from the old table to the new table
            DB::statement("INSERT INTO works_temp (id, ark, pref_title, json, created_at, updated_at) SELECT id, '' as ark, pref_title, json, created_at, updated_at FROM works");

            Schema::dropIfExists('works');
            Schema::rename('works_temp', 'works');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('works', function (Blueprint $table) {
            $table->dropColumn('ark');
        });
    }
};
