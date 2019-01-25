<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSalesPerDaysTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sales_per_days', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('invoice_number', 10)->nullable();
			$table->integer('order_id')->nullable();
			$table->date('order_date')->nullable();
			$table->text('name', 65535)->nullable();
			$table->string('sku')->nullable();
			$table->integer('quantity')->nullable();
			$table->float('cost', 10)->nullable();
			$table->float('unit_price')->nullable();
			$table->float('margin')->nullable();
			$table->timestamps();
			$table->integer('sales_person_id')->nullable();
			$table->char('code', 10)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sales_per_days');
	}

}
