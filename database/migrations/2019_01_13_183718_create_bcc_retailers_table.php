<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBccRetailersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bcc_retailers', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->boolean('is_odoo')->default(0);
			$table->integer('sales_person_id')->nullable();
			$table->string('sales_person_name')->nullable();
			$table->string('license')->nullable();
			$table->string('designation')->nullable();
			$table->string('business_name')->nullable();
			$table->string('dba')->nullable();
			$table->string('status', 20)->nullable();
			$table->string('expiration_date', 20)->nullable();
			$table->string('business_structure')->nullable();
			$table->string('city')->nullable()->index('city');
			$table->integer('zip')->nullable()->index('zip');
			$table->string('phone')->nullable();
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
		Schema::drop('bcc_retailers');
	}

}
