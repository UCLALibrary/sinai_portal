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
        Schema::create('locations', function (Blueprint $table) {
            $table->string('id', 255)->primary();
			$table->string('collection');
			$table->string('repository');
			$table->string('note')->nullable();
			$table->string('country')->nullable();
			$table->string('address')->nullable();
			$table->string('contact_info')->nullable();
			$table->string('coordinates')->nullable();
			$table->string('url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
