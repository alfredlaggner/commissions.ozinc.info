<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Earning2 extends Model
{
	protected $fillable = ['sales_person_id', 'commission','name','sale','month','year','updated_at', 'created_at'];
	protected $table = 'earnings2';

	public function salesperson()
	{
		return $this->hasOne('App\SalesPerson', 'sales_person_id', 'sales_person_id');
	}
}
