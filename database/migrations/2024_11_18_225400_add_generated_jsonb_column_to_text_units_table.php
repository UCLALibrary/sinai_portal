<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE text_units ADD COLUMN jsonb jsonb GENERATED ALWAYS AS (json::jsonb) STORED");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE text_units DROP COLUMN jsonb");
    }
};
