<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class SalesPerson extends Model
{
    protected $table = 'sales_persons';

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('age', function (Builder $builder) {
            $builder->where('is_salesperson', '=', 1);
        });
    }
	public function salesperson()
	{
		return $this->hasOne('App\SaleInvoice', 'sales_person_id', 'sales_person_id');
	}

}