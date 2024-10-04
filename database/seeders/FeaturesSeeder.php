<?php

namespace Database\Seeders;

use App\Models\Feature;
use App\Models\FormContext;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FeaturesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ms_objects = FormContext::where('form_context', 'ms_objects')->first()->id;
        $parts = FormContext::where('form_context', 'parts')->first()->id;
        $content_units = FormContext::where('form_context', 'content_units')->first()->id;
        $layers = FormContext::where('form_context', 'layers')->first()->id;
        $ornamentation = FormContext::where('form_context', 'ornamentation')->first()->id;

        Feature::create(['term' => 'Calligraphic Practice'])->formContexts()->attach([$ms_objects, $parts]);
        Feature::create(['term' => 'Table(s)', 'corresp_note' => 'contents note'])->formContexts()->attach([$ms_objects, $parts, $content_units, $layers]);
        Feature::create(['term' => 'Table(s) of Contents', 'corresp_note' => 'contents note'])->formContexts()->attach([$ms_objects, $content_units, $layers]);

        Feature::create(['term' => 'Unidentified Text', 'corresp_note' => 'contents note'])->formContexts()->attach([$content_units]);
        Feature::create(['term' => 'Music notation', 'corresp_note' => 'contents note'])->formContexts()->attach([$content_units]);
        Feature::create(['term' => 'Parallel text', 'corresp_note' => 'layout note'])->formContexts()->attach([$content_units]);

        Feature::create(['term' => 'Catchwords', 'corresp_note' => 'collation'])->formContexts()->attach([$parts]);
        Feature::create(['term' => 'Signatures', 'corresp_note' => 'collation'])->formContexts()->attach([$layers]);

        Feature::create(['term' => 'Colophon', 'corresp_note' => 'para.type:colophon or contents note'])->formContexts()->attach([$layers]);
        Feature::create(['term' => 'Dated', 'corresp_note' => 'assoc_date.type:origin'])->formContexts()->attach([$layers]);

        Feature::create(['term' => 'Palimpsest', 'corresp_note' => 'contents note'])->formContexts()->attach([$ms_objects, $parts]);
        Feature::create(['term' => 'Inscription(s)'])->formContexts()->attach([$ms_objects, $parts]);
        Feature::create(['term' => 'Fragment', 'corresp_note' => 'condition note'])->formContexts()->attach([$ms_objects, $parts]);
        Feature::create(['term' => 'Multispectral Imaging'])->formContexts()->attach([$ms_objects, $parts]);

        Feature::create(['term' => 'Border(s)', 'corresp_note' => 'ornamentation'])->formContexts()->attach([$ornamentation]);
        Feature::create(['term' => 'Border(s), Decorative', 'corresp_note' => 'ornamentation'])->formContexts()->attach([$ornamentation]);
        Feature::create(['term' => 'Decoration, Anthropomorphic', 'corresp_note' => 'ornamentation'])->formContexts()->attach([$ornamentation]);
        Feature::create(['term' => 'Decoration, Architectural', 'corresp_note' => 'ornamentation'])->formContexts()->attach([$ornamentation]);
        Feature::create(['term' => 'Decoration, Calligraphic', 'corresp_note' => 'ornamentation'])->formContexts()->attach([$ornamentation]);
        Feature::create(['term' => 'Decoration, Full-page', 'corresp_note' => 'ornamentation'])->formContexts()->attach([$ornamentation]);
        Feature::create(['term' => 'Decoration, Geometric', 'corresp_note' => 'ornamentation'])->formContexts()->attach([$ornamentation]);
        Feature::create(['term' => 'Decoration, Vegetative', 'corresp_note' => 'ornamentation'])->formContexts()->attach([$ornamentation]);
        Feature::create(['term' => 'Decoration, Zoomorphic', 'corresp_note' => 'ornamentation'])->formContexts()->attach([$ornamentation]);
        Feature::create(['term' => 'Headpiece(s)', 'corresp_note' => 'ornamentation'])->formContexts()->attach([$ornamentation]);
        Feature::create(['term' => 'Illumination(s)', 'corresp_note' => 'ornamentation'])->formContexts()->attach([$ornamentation]);
        Feature::create(['term' => 'Illustration(s)', 'corresp_note' => 'ornamentation'])->formContexts()->attach([$ornamentation]);
        Feature::create(['term' => 'Initial(s), Anthropomorphic', 'corresp_note' => 'ornamentation'])->formContexts()->attach([$ornamentation]);
        Feature::create(['term' => 'Initial(s), Decorated', 'corresp_note' => 'ornamentation'])->formContexts()->attach([$ornamentation]);
        Feature::create(['term' => 'Initial(s), Gymnastic', 'corresp_note' => 'ornamentation'])->formContexts()->attach([$ornamentation]);
        Feature::create(['term' => 'Initial(s), Historiated', 'corresp_note' => 'ornamentation'])->formContexts()->attach([$ornamentation]);
        Feature::create(['term' => 'Initial(s), Inhabited', 'corresp_note' => 'ornamentation'])->formContexts()->attach([$ornamentation]);
        Feature::create(['term' => 'Initial(s), Zoomorphic', 'corresp_note' => 'ornamentation'])->formContexts()->attach([$ornamentation]);
        Feature::create(['term' => 'Miniature(s)', 'corresp_note' => 'ornamentation'])->formContexts()->attach([$ornamentation]);

    }
}
