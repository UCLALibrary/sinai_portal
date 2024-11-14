<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            CREATE OR REPLACE VIEW layer_text_units AS
            SELECT
                l.id AS layer_id,
                substring(text_units.text_unit_data->>'id' from '[^/]+$') AS text_unit_id,
                text_units.text_unit_index - 1 AS text_unit_index,
                text_units.text_unit_data->>'label' AS text_unit_label,
                text_units.text_unit_data
            FROM
                layers l
                CROSS JOIN LATERAL jsonb_array_elements(l.jsonb->'text_unit') WITH ORDINALITY AS text_units(text_unit_data, text_unit_index)
            WHERE
                jsonb_exists(l.jsonb, 'text_unit')
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS layer_text_units");
    }
};
