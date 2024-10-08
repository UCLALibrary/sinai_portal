<?php

namespace App\Models;

use App\Traits\JsonSchemas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory, JsonSchemas;

	protected $keyType = 'string';
	public $incrementing = false;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'id',
		'collection',
		'repository',
		'note',
		'country',
		'address',
		'contact_info',
		'coordinates',
		'url',
	];
}

Location::initialize('location');