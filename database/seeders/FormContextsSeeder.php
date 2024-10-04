<?php

namespace Database\Seeders;

use App\Models\FormContext;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FormContextsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FormContext::insert([
            ['form_context' => 'ms_objects'],
            ['form_context' => 'parts'],
            ['form_context' => 'content_units'],
            ['form_context' => 'layers'],
            ['form_context' => 'ornamentation']
        ]);
    }
}
