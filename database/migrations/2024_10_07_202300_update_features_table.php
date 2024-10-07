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
		Schema::table('features', function (Blueprint $table) {
			$table->dropPrimary('features_pkey');
		});

		Schema::table('features', function (Blueprint $table) {
			$table->string('id', 255)->change();
			$table->renameColumn('term', 'label');
		});

		Schema::table('features', function (Blueprint $table) {
			$table->primary('id');
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
