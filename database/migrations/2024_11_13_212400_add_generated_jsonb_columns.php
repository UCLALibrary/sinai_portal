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
        $tables = [
            'agents',
            'bibliographies',
            'contents',
            'layers',
            'manuscripts',
            'parts',
            'places',
            'works',
            'text_units',
        ];

        foreach ($tables as $table) {
            DB::statement("ALTER TABLE $table ADD COLUMN jsonb jsonb GENERATED ALWAYS AS (json::jsonb) STORED");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'agents',
            'bibliographies',
            'contents',
            'layers',
            'manuscripts',
            'parts',
            'places',
            'works',
            'text_units',
        ];

        foreach ($tables as $table) {
            DB::statement("ALTER TABLE $table DROP COLUMN jsonb");
        }
    }
};
