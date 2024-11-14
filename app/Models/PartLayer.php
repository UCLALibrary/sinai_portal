<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartLayer extends Model
{
    protected $table = 'part_layers';

    // set the primary key to null since views do not have primary keys
    protected $primaryKey = null;
    public $incrementing = false;

    // disable timestamps since they are not included in the view
    public $timestamps = false;

    /**
     * Relationships
     */

    public function part()
    {
        return $this->belongsTo(ManuscriptPart::class);
    }
}
