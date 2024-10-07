<?php

namespace App\Models;

use App\Traits\JsonSchemas;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Feature extends Model
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
        'label',
        'corresp_note',
        'summary',
        'scope',
    ];

	public static function booted() {
		static::creating(function ($model) {
			if(!isset($model->id)) {
				$model->id = Str::slug($model->label, '-');
			}
		});
	}

    public function formContexts(): BelongsToMany
    {
        return $this->belongsToMany(FormContext::class);
    }
}

/*
 * Execute the static initializer to load the schemas for JSON Forms.
 */
Feature::initialize('feature');