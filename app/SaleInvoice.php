<?php

	namespace App;

	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\Builder;

	class SaleInvoice extends Model
	{
		protected $table = 'saleinvoices';

		protected $fillable = ['commission', 'comm_percent', 'comm_version', 'comm_region', 'updated_at', 'created_at'];

		protected static function boot()
		{
			parent::boot();

			static::addGlobalScope('age', function (Builder $builder) {
				$builder->where('invoice_status', '=', 'invoiced')
					->where('saleinvoices.sales_person_id', '>', 0)
					->where('saleinvoices.margin', '>', -100)
					->where('saleinvoices.margin', '<', 100);
			});
		}

		public function salesperson()
		{
			return $this->hasOne('App\SalesPerson', 'sales_person_id', 'sales_person_id');
		}

		public function customer()
		{
			return $this->belongsTo('App\Customer', 'customer_id', 'ext_id');
		}

	}
