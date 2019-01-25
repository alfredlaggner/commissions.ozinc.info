<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEarningsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('earnings', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('year')->nullable();
			$table->integer('month')->nullable();
			$table->integer('sales_person_id')->nullable();
			$table->float('commission', 10, 0)->nullable();
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
		Schema::drop('earnings');
	}

}
