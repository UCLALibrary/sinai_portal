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
		Schema::table('feature_form_context', function (Blueprint $table) {
			$table->foreign('feature_id')->references('id')->on('features');
		});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
		//
    }
};
