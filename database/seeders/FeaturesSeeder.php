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

        Feature::create(['id' => 'calligraphic-practice', 'label' => 'Calligraphic Practice'])->formContexts()->attach([$ms_objects, $parts]);
        Feature::create(['id' => 'table', 'label' => 'Table(s)', 'corresp_note' => 'contents note'])->formContexts()->attach([$ms_objects, $parts, $content_units, $layers]);
        Feature::create(['id' => 'toc', 'label' => 'Table(s) of Contents', 'corresp_note' => 'contents note'])->formContexts()->attach([$ms_objects, $content_units, $layers]);

        Feature::create(['id' => 'unidentified-text', 'label' => 'Unidentified Text', 'corresp_note' => 'contents note'])->formContexts()->attach([$content_units]);
        Feature::create(['id' => 'music-notation', 'label' => 'Music notation', 'corresp_note' => 'contents note'])->formContexts()->attach([$content_units]);
        Feature::create(['id' => 'parallel-text', 'label' => 'Parallel text', 'corresp_note' => 'layout note'])->formContexts()->attach([$content_units]);

        Feature::create(['id' => 'catchwords', 'label' => 'Catchwords', 'corresp_note' => 'collation'])->formContexts()->attach([$parts]);
        Feature::create(['id' => 'signatures', 'label' => 'Signatures', 'corresp_note' => 'collation'])->formContexts()->attach([$layers]);

        Feature::create(['id' => 'colophon', 'label' => 'Colophon', 'corresp_note' => 'para.type:colophon or contents note'])->formContexts()->attach([$layers]);
        Feature::create(['id' => 'dated', 'label' => 'Dated', 'corresp_note' => 'assoc_date.type:origin'])->formContexts()->attach([$layers]);

        Feature::create(['id' => 'palimpsest', 'label' => 'Palimpsest', 'corresp_note' => 'contents note'])->formContexts()->attach([$ms_objects, $parts]);
        Feature::create(['id' => 'inscription', 'label' => 'Inscription(s)'])->formContexts()->attach([$ms_objects, $parts]);
        Feature::create(['id' => 'fragment', 'label' => 'Fragment', 'corresp_note' => 'condition note'])->formContexts()->attach([$ms_objects, $parts]);
        Feature::create(['id' => 'msi', 'label' => 'Multispectral Imaging'])->formContexts()->attach([$ms_objects, $parts]);

        Feature::create(['id' => 'border', 'label' => 'Border(s)', 'corresp_note' => 'ornamentation'])->formContexts()->attach([$ornamentation]);
        Feature::create(['id' => 'border-deco', 'label' => 'Border(s), Decorative', 'corresp_note' => 'ornamentation'])->formContexts()->attach([$ornamentation]);
        Feature::create(['id' => 'deco-anthropomorph', 'label' => 'Decoration, Anthropomorphic', 'corresp_note' => 'ornamentation'])->formContexts()->attach([$ornamentation]);
        Feature::create(['id' => 'deco-architectural', 'label' => 'Decoration, Architectural', 'corresp_note' => 'ornamentation'])->formContexts()->attach([$ornamentation]);
        Feature::create(['id' => 'deco-calligraphic', 'label' => 'Decoration, Calligraphic', 'corresp_note' => 'ornamentation'])->formContexts()->attach([$ornamentation]);
        Feature::create(['id' => 'deco-full-page', 'label' => 'Decoration, Full-page', 'corresp_note' => 'ornamentation'])->formContexts()->attach([$ornamentation]);
        Feature::create(['id' => 'deco-geometric', 'label' => 'Decoration, Geometric', 'corresp_note' => 'ornamentation'])->formContexts()->attach([$ornamentation]);
        Feature::create(['id' => 'deco-vegetative', 'label' => 'Decoration, Vegetative', 'corresp_note' => 'ornamentation'])->formContexts()->attach([$ornamentation]);
        Feature::create(['id' => 'deco-zoomorph', 'label' => 'Decoration, Zoomorphic', 'corresp_note' => 'ornamentation'])->formContexts()->attach([$ornamentation]);
        Feature::create(['id' => 'headpiece', 'label' => 'Headpiece(s)', 'corresp_note' => 'ornamentation'])->formContexts()->attach([$ornamentation]);
        Feature::create(['id' => 'illumination', 'label' => 'Illumination(s)', 'corresp_note' => 'ornamentation'])->formContexts()->attach([$ornamentation]);
        Feature::create(['id' => 'illustration', 'label' => 'Illustration(s)', 'corresp_note' => 'ornamentation'])->formContexts()->attach([$ornamentation]);
        Feature::create(['id' => 'initials-anthropomorph', 'label' => 'Initial(s), Anthropomorphic', 'corresp_note' => 'ornamentation'])->formContexts()->attach([$ornamentation]);
        Feature::create(['id' => 'initials-deco', 'label' => 'Initial(s), Decorated', 'corresp_note' => 'ornamentation'])->formContexts()->attach([$ornamentation]);
        Feature::create(['id' => 'initials-gymnastics', 'label' => 'Initial(s), Gymnastic', 'corresp_note' => 'ornamentation'])->formContexts()->attach([$ornamentation]);
        Feature::create(['id' => 'initials-historiated', 'label' => 'Initial(s), Historiated', 'corresp_note' => 'ornamentation'])->formContexts()->attach([$ornamentation]);
        Feature::create(['id' => 'initials-inhabited', 'label' => 'Initial(s), Inhabited', 'corresp_note' => 'ornamentation'])->formContexts()->attach([$ornamentation]);
        Feature::create(['id' => 'initials-zoomorph', 'label' => 'Initial(s), Zoomorphic', 'corresp_note' => 'ornamentation'])->formContexts()->attach([$ornamentation]);
        Feature::create(['id' => 'miniature', 'label' => 'Miniature(s)', 'corresp_note' => 'ornamentation'])->formContexts()->attach([$ornamentation]);

    }
}
