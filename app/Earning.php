<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Earning extends Model
{
    protected $fillable = ['sales_person_id', 'commission','month','year','updated_at', 'created_at'];

    public function salesperson()
    {
        return $this->hasOne('App\SalesPerson', 'sales_person_id', 'sales_person_id');
    }

}
