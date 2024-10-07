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
			$table->dropForeign('feature_form_context_feature_id_foreign');
			$table->string('feature_id', 255)->change();
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
