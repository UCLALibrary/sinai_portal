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
    public function up(): void
    {
        // Create a new table with the correct column order
        Schema::create('manuscripts_temp', function (Blueprint $table) {
            $table->id();
            $table->string('ark');
            $table->string('type'); // Moving 'type' to be after 'ark'
            $table->string('identifier');
            $table->jsonb('json');
            $table->timestamps();
        });

        // Copy data from the old table to the new table
        DB::statement('INSERT INTO manuscripts_temp (id, ark, type, identifier, json, created_at, updated_at) SELECT id, ark, type, identifier, json, created_at, updated_at FROM manuscripts');

        Schema::dropIfExists('manuscripts');
        Schema::rename('manuscripts_temp', 'manuscripts');
    }

    public function down(): void
    {
        // Recreate the original table structure with 'type' after 'timestamps'
        Schema::create('manuscripts_temp', function (Blueprint $table) {
            $table->id();
            $table->string('ark');
            $table->string('identifier');
            $table->jsonb('json');
            $table->timestamps();
            $table->string('type');
        });

        // Copy data from the modified table back to the original structure
        DB::statement('INSERT INTO manuscripts_temp (id, ark, identifier, json, created_at, updated_at, type) SELECT id, ark, identifier, json, created_at, updated_at, type FROM manuscripts');

        Schema::dropIfExists('manuscripts');
        Schema::rename('manuscripts_temp', 'manuscripts');
    }

};
