<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCustomersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('customers', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('ext_id')->nullable()->index();
			$table->string('ext_id_contact')->nullable()->index();
			$table->string('license', 50)->nullable();
			$table->string('license2')->nullable();
			$table->string('name')->nullable();
			$table->string('street')->nullable();
			$table->string('street2')->nullable();
			$table->string('city')->nullable();
			$table->string('zip')->nullable();
			$table->string('phone', 50)->nullable();
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
		Schema::drop('customers');
	}

}
