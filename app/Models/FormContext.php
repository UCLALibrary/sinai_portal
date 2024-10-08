<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormContext extends Model
{
    use HasFactory;

    public function features()
    {
        return $this->belongsToMany(Feature::class);
    }
}
