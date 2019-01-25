<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCaliforniaZipcodesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('california_zipcodes', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('sales_person_id')->nullable()->index('sales_person_id');
			$table->integer('zip')->nullable()->index('zip');
			$table->string('city', 24)->nullable()->index('city');
			$table->string('county', 32)->nullable();
			$table->dateTime('updated_at')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('california_zipcodes');
	}

}
