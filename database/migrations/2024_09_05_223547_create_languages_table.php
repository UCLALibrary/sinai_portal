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
            $table->string('id')->primary();
            $table->string('label');
            $table->string('iso')->nullable();
            $table->string('glottolog')->nullable();
            $table->string('writing_systems')->nullable();
            $table->string('other_names')->nullable();
            $table->string('when_in_use')->nullable();
            $table->string('regions')->nullable();
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
