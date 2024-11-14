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
	    Schema::table('places', function (Blueprint $table) {
		    $table->dropPrimary('places_pkey');
	    });
	    
	    Schema::table('places', function (Blueprint $table) {
		    $table->string('id', 255)->change();
	    });
	    
	    Schema::table('places', function (Blueprint $table) {
		    $table->primary('id');
	    });
	    
	    Schema::table('places', function (Blueprint $table) {
		    $table->string('ark', 255)->nullable();
	    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
	    Schema::table('places', function (Blueprint $table) {
		    $table->dropColumn('ark');
	    });
		
	    Schema::table('places', function (Blueprint $table) {
		    $table->dropPrimary('places_pkey');
	    });
	    
	    Schema::table('places', function (Blueprint $table) {
		    DB::statement('ALTER TABLE places ALTER COLUMN id TYPE bigint USING id::bigint');
	    });
	    
	    Schema::table('places', function (Blueprint $table) {
		    $table->primary('id');
	    });
    }
};
