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
        Schema::create('feature_form_context', function (Blueprint $table) {
            $table->unsignedBigInteger('feature_id');
            $table->unsignedBigInteger('form_context_id');

            $table->foreign('feature_id')->references('id')->on('features');
            $table->foreign('form_context_id')->references('id')->on('form_contexts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feature_form_context');
    }
};
