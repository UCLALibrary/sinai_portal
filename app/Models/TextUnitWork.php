<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TextUnitWork extends Model
{
    protected $table = 'text_unit_works';

    // set the primary key to null since views do not have primary keys
    protected $primaryKey = null;
    public $incrementing = false;

    // disable timestamps since they are not included in the view
    public $timestamps = false;

    /**
     * Relationships
     */

    public function textUnit()
    {
        return $this->belongsTo(TextUnit::class);
    }
}
