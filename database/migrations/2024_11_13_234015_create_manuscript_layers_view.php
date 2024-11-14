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
            CREATE OR REPLACE VIEW manuscript_layers AS
            SELECT
                m.id AS manuscript_id,
                substring(layers.layer_data->>'id' from '[^/]+$') AS layer_id,
                layers.layer_index - 1 AS layer_index,
                layers.layer_data->'type'->>'id' AS layer_type,
                layers.layer_data->>'label' AS layer_label,
                layers.layer_data
            FROM
                manuscripts m,
                LATERAL jsonb_array_elements(m.jsonb->'layer') WITH ORDINALITY AS layers(layer_data, layer_index)
            WHERE
                jsonb_exists(m.jsonb, 'layer')
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS manuscript_layers");
    }
};
