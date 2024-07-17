<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class Person extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'persons';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'role',
        'as_written',
        'json',
    ];

    public static $schema;

    public static $uiSchema;

    public static function initialize() {
        self::$schema = File::get(base_path('/schemas/json/complete/assoc_name.json'));
        self::$uiSchema = File::get(base_path('/schemas/ui/complete/assoc_name.json'));
    }
}

/*
 * Execute the static initializer to load the schema and ui schema.
 */
Person::initialize();
