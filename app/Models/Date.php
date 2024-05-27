<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class Date extends Model
{
    use HasFactory;

    public static $schema;

    public static $uischema;

    public static function initialize() {
        self::$schema = File::get(base_path('/schemas/assoc_date.json'));
        self::$uischema = File::get(base_path('/schemas/ui/assoc_date.json'));
    }
}
