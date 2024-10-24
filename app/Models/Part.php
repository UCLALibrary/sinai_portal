<?php

namespace App\Models;

use App\Traits\JsonSchemas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Part extends Model
{
    use HasFactory, JsonSchemas;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'ark',
        'identifier',
        'json',
    ];

    /**
     * Note: The order of the values must align with the order of the fields in the $fillable array.
     */
    public function getFillableFields($data, $json)
    {
        $identifier = '';
        if (isset($jsonData['idno']) && is_array($jsonData['idno'])) {
            foreach ($jsonData['idno'] as $idno) {
                $label = $idno['type'] === 'shelfmark'
                    ? 'Shelfmark'
                    : ($idno['type'] === 'part_no'
                        ? 'Part'
                        : ($idno['type'] === 'uto_mark'
                            ? 'UTO Mark'
                            : ''));
                $identifier = $label . ': ' . $idno['value'];
                break;
            }
        }

        return array_combine($this->fillable, [
            basename($data['ark']),  // use the trailing ark segment as the id
            $data['ark'],
            $identifier,
            $json,
        ]);
    }

    public static $config = [
        'index' => [
            'columns' => [
                'ark' => 'ARK',
                'identifier' => 'Identifier',
            ],
        ],
    ];
}

/*
 * Execute the static initializer to load the schemas for JSON Forms.
 */
Part::initialize('cod_unit');
