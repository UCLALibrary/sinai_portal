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
