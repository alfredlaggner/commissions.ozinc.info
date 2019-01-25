<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBusinessesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('businesses', function(Blueprint $table)
		{
			$table->integer('id')->unsigned()->primary();
			$table->char('name', 50);
			$table->char('adult_license_name', 50);
			$table->char('adult_license_number', 50);
			$table->char('medicinal_license_name', 50);
			$table->char('medicinal_license_number', 50);
			$table->char('street', 50);
			$table->char('city', 50);
			$table->char('zip', 50);
			$table->char('phone', 50);
			$table->char('email', 50);
			$table->char('contact', 50);
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
		Schema::drop('businesses');
	}

}
