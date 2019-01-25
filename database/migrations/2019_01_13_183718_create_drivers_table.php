<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDriversTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('drivers', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('sales_person_id')->nullable();
			$table->char('first_name', 50)->nullable();
			$table->char('last_name', 50)->nullable();
			$table->char('license', 50)->nullable();
			$table->string('name')->nullable();
			$table->string('email')->nullable();
			$table->string('phone_number')->nullable();
			$table->string('password')->nullable();
			$table->string('remember_token', 100)->nullable()->default('NULL');
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
		Schema::drop('drivers');
	}

}
