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
            CREATE OR REPLACE VIEW part_layers AS
            SELECT
                mp.manuscript_id,
                mp.part_index,
                substring(layers.layer_data->>'id' from '[^/]+$') AS layer_id,
                layers.layer_index - 1 AS layer_index,
                layers.layer_data->'type'->>'id' AS layer_type,
                layers.layer_data->>'label' AS layer_label,
                layers.layer_data
            FROM
                manuscript_parts mp,
                LATERAL jsonb_array_elements(mp.part_data->'layer') WITH ORDINALITY AS layers(layer_data, layer_index)
            WHERE
                jsonb_exists(mp.part_data, 'layer')
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS part_layers");
    }
};
