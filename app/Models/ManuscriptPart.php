<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManuscriptPart extends Model
{
    protected $table = 'manuscript_parts';

    // set the primary key to null since views do not have primary keys
    protected $primaryKey = null;
    public $incrementing = false;

    // disable timestamps since they are not included in the view
    public $timestamps = false;

    /**
     * Relationships
     */

    protected $with = ['partLayers', 'layers'];

    public function manuscript()
    {
        return $this->belongsTo(Manuscript::class);
    }

    public function partLayers()
    {
        return $this->hasMany(
            PartLayer::class,
            'manuscript_id',    // foreign key on the PartLayer table
            'manuscript_id'     // local key on the ManuscriptPart table
        );
    }

    public function layers()
    {
        return $this->hasManyThrough(
            Layer::class,        // the target model you want to access
            PartLayer::class,    // the intermediate model
            'manuscript_id',     // foreign key on the PartLayer table
            'id',                // foreign key on the Layer table
            'manuscript_id',     // local key on the ManuscriptPart table
            'layer_id'           // local key on the PartLayer table
        );
    }
}
