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
            CREATE OR REPLACE VIEW text_unit_works AS
            SELECT
                tu.id AS text_unit_id,
                substring(works.work_data->'work'->>'id' from '[^/]+$') AS work_id,
                works.work_index - 1 AS work_index,
                works.work_data->>'alt_title' AS work_alt_title,
                works.work_data
            FROM
                text_units tu
                CROSS JOIN LATERAL jsonb_array_elements(tu.jsonb->'work_wit') WITH ORDINALITY AS works(work_data, work_index)
            WHERE
                jsonb_exists(tu.jsonb, 'work_wit')
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS text_unit_works");
    }
};
