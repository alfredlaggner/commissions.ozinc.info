<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Brand extends Model
{
	protected static function boot()
	{
		parent::boot();

		static::addGlobalScope('active', function (Builder $builder) {
			$builder->where('is_active', '=', 1);
		});
	}
	protected $fillable = ['is_active'];
}
