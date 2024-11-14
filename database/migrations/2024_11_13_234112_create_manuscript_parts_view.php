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
            CREATE OR REPLACE VIEW manuscript_parts AS
            SELECT
                m.id AS manuscript_id,
                parts.part_index - 1 AS part_index,
                parts.part_data->>'label' AS part_label,
                parts.part_data
            FROM
                manuscripts m,
                LATERAL jsonb_array_elements(m.jsonb->'part') WITH ORDINALITY AS parts(part_data, part_index)
            WHERE
                jsonb_exists(m.jsonb, 'part')
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS manuscript_parts");
    }
};
