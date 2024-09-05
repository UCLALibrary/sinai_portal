<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->string('id', 8)->primary();
            $table->string('label', 50);
            $table->string('iso', 3)->nullable();
            $table->string('glottolog', 8)->nullable();
            $table->string('writing_systems', 100)->nullable();
            $table->string('other_names', 100)->nullable();
            $table->string('when_in_use', 150)->nullable();
            $table->string('regions', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('languages');
    }
};
