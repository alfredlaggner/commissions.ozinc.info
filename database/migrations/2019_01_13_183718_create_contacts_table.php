<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateContactsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('contacts', function(Blueprint $table)
		{
			$table->integer('id')->unsigned()->primary();
			$table->string('ext_id', 50)->nullable()->index();
			$table->string('customer_id', 50)->nullable();
			$table->string('name')->nullable();
			$table->string('phone', 50)->nullable();
			$table->timestamps();
			$table->integer('driver_id')->nullable();
			$table->integer('sale_order_id')->nullable();
			$table->integer('vehicle_id')->nullable();
			$table->integer('salesperson_id')->nullable();
			$table->dateTime('delivered_at')->nullable();
			$table->text('notes', 65535)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('contacts');
	}

}
