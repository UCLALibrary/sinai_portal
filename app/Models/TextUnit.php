<?php

namespace App\Models;

use App\Traits\HasRelatedEntities;
use App\Traits\JsonSchemas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class TextUnit extends Model
{
    use HasFactory, JsonSchemas, Searchable, HasRelatedEntities;
    
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'id',
        'ark',
        'label',
        'json',
    ];
    
    public function getFillableFields($data, $json)
    {
        return array_combine($this->fillable, [
            basename($data['ark']),  // use the trailing ark segment as the id
            $data['ark'],
            $data['label'],
            $json,
        ]);
    }
    
    public static $config = [
        'index' => [
            'columns' => [
                'id' => 'Id',
                'ark' => 'Ark',
                'label' => 'Label',
            ],
        ],
    ];

    /**
     * Get the parent layers.
     *
     * @return string|null
     */
    public function getParentLayers()
    {
        $data = $this->getJsonData();

        return !empty($data['parent'])
            ? Layer::whereIn('ark', $data['parent'])->get()
            : null;
    }

    public function toSearchableArray(): array
    {
        $array = $this->toArray();

        $array['ark'] = $this->ark ?? null;
        $array['label'] = $this->label ?? null;

        // min and max date from the 'assoc_date' field from layers of type 'origin'
        foreach($this->getParentLayers() as $layer) {
            $data = $layer->getJsonData();

            if (isset($data['assoc_date'])) {
                $notBeforeValues = [];
                $notAfterValues = [];
                foreach($data['assoc_date'] as $date) {
                    if (isset($date['type']['id']) && $date['type']['id'] === 'origin') {
                        $notBeforeValues[] = $date['iso']['not_before'] ?? null;
                        $notAfterValues[] = $date['iso']['not_after'] ?? null;
                    }
                }
    
                // minimum date from the 'not_before' field from layers of type 'origin'
                $values = array_filter($notBeforeValues, fn($value) => $value !== null);
                $array['date_min'] = $values ? min(array_map('intval', $values)) : null;
        
                // maximum date from the 'not_after' field from layers of type 'origin'
                $values = array_filter($notAfterValues, fn($value) => $value !== null);
                $array['date_max'] = $values ? max(array_map('intval', $values)) : null;
            }
        }

        /*
         * Apply default transformations if desired.
         *
         * https://www.algolia.com/doc/framework-integration/laravel/indexing/configure-searchable-data/?client=php#transformers
         */
        // $array = $this->transform($array);

        return $array;
    }
}

/*
 * Execute the static initializer to load the schemas for JSON Forms.
 */
TextUnit::initialize('text_unit');
