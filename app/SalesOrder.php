<?php

namespace App;
use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{

    protected $table = 'salesorders';
	protected $fillable = ['sales_order','sales_order_id','order_date','amount_untaxed','amount_total','amount_tax','deliver_date','salesperson_id','customer_id'];

    public function saleinvoice()
    {
        return $this->hasMany('App\SaleInvoice', 'order_id', 'sales_order_id');
    }

}
