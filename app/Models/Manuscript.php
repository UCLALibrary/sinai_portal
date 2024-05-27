<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class Manuscript extends Model
{
    use HasFactory;

    protected $fillable = ['json'];

    public static $schema;

    public static $uischema;

    public static function initialize() {
        self::$schema = File::get(base_path('/schemas/ms-obj.json'));
        self::$uischema = File::get(base_path('/schemas/ui/ms-obj.json'));
    }
}
