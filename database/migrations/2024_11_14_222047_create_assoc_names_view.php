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
            CREATE OR REPLACE VIEW assoc_names AS

            SELECT
                substring(m.ark from '[^/]+$') AS parent_id,
                substring(assoc_name_elem->>'id' from '[^/]+$') AS agent_id,
                a.jsonb->>'pref_name' AS agent_pref_name
            FROM
                manuscripts m
            JOIN LATERAL
                jsonb_array_elements(m.jsonb->'assoc_name') AS assoc_name_elem
                ON TRUE
            JOIN
                agents a
                ON a.id = substring(assoc_name_elem->>'id' from '[^/]+$')
            WHERE
                jsonb_typeof(m.jsonb->'assoc_name') = 'array'

            UNION ALL

            SELECT
                substring(l.ark from '[^/]+$') AS parent_id,
                substring(assoc_name_elem->>'id' from '[^/]+$') AS agent_id,
                a.jsonb->>'pref_name' AS agent_pref_name
            FROM
                layers l
            JOIN LATERAL
                jsonb_array_elements(l.jsonb->'assoc_name') AS assoc_name_elem
                ON TRUE
            JOIN
                agents a
                ON a.id = substring(assoc_name_elem->>'id' from '[^/]+$')
            WHERE
                jsonb_typeof(l.jsonb->'assoc_name') = 'array'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS assoc_names");
    }
};
