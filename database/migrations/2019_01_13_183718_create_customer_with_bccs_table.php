<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCustomerWithBccsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('customer_with_bccs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('ext_id')->nullable()->index('customers_ext_id_index');
			$table->string('ext_id_contact')->nullable()->index('customers_ext_id_contact_index');
			$table->string('license', 50)->nullable();
			$table->string('license2')->nullable();
			$table->string('name')->nullable();
			$table->string('dba')->nullable();
			$table->string('street')->nullable();
			$table->string('street2')->nullable();
			$table->string('city')->nullable();
			$table->string('zip')->nullable();
			$table->string('phone', 50)->nullable();
			$table->timestamps();
			$table->boolean('is_bcc')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('customer_with_bccs');
	}

}
