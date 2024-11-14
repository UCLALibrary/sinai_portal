<?php

namespace App\Models;

use App\Traits\JsonSchemas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class TextUnit extends Model
{
    use HasFactory, JsonSchemas, Searchable;

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
     * Relationships
     */

    protected $with = ['textUnitWorks', 'works'];

    public function layer()
    {
        return $this->belongsTo(Layer::class);
    }

    public function textUnitWorks()
    {
        return $this->hasMany(TextUnitWork::class);
    }

    public function works()
    {
        return $this->hasManyThrough(
            Work::class,            // the target model you want to access
            TextUnitWork::class,    // the intermediate model
            'text_unit_id',         // foreign key on the TextUnitWork table
            'id',                   // foreign key on the Work table
            'id',                   // local key on the TextUnit table
            'work_id'               // local key on the TextUnitWork table
        );
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray(): array
    {
        $array = $this->toArray();
        
        $array['ark'] = $this->ark ?? null;
        $array['label'] = $this->label ?? null;
        
        /*
         * Apply default transformations if desired.
         *
         * https://www.algolia.com/doc/framework-integration/laravel/indexing/configure-searchable-data/?client=php#transformers
         */
        // $array = $this->transform($array);
        
        return $array;
    }
}
TextUnit::initialize('text_unit');
