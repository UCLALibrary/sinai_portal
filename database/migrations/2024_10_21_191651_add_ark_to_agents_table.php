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
            Schema::create('agents_temp', function (Blueprint $table) {
                $table->string('id', 255)->primary();
                $table->string('ark');
                $table->string('type');
                $table->string('pref_name');
                $table->jsonb('json');
                $table->timestamps();
            });

            // copy data from the old table to the new table
            DB::statement("INSERT INTO agents_temp (id, ark, type, pref_name, json, created_at, updated_at) SELECT id, '' as ark, type, pref_name, json, created_at, updated_at FROM agents");

            Schema::dropIfExists('agents');
            Schema::rename('agents_temp', 'agents');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->dropColumn('ark');
        });
    }
};
