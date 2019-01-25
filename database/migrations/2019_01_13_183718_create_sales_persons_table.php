<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSalesPersonsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sales_persons', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('sales_person_id')->nullable();
			$table->boolean('is_salesperson')->default(0);
			$table->string('name')->nullable();
			$table->string('email')->nullable();
			$table->string('phone_number')->nullable();
			$table->string('region', 1)->nullable();
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sales_persons');
	}

}
