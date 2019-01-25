<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEarnings2Table extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('earnings2', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('year')->nullable();
			$table->integer('month')->nullable();
			$table->integer('sales_person_id')->nullable();
			$table->string('name')->nullable();
			$table->float('sale', 10, 4)->nullable();
			$table->float('commission', 10, 4)->nullable();
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
		Schema::drop('earnings2');
	}

}
