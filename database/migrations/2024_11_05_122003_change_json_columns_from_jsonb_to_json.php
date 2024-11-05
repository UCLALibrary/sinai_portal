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
            'works'
        ];

        foreach ($tables as $table) {
            DB::statement("ALTER TABLE $table ALTER COLUMN json TYPE json USING json::json");
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
            'works'
        ];

        foreach ($tables as $table) {
            DB::statement("ALTER TABLE $table ALTER COLUMN json TYPE jsonb USING json::jsonb");
        }
    }
};
