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

    public function manuscript()
    {
        return $this->belongsTo(Manuscript::class);
    }
}
