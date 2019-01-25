<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateZipCodesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('zip_codes', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('zip')->nullable();
			$table->string('city')->nullable()->index('city');
			$table->string('state');
			$table->decimal('lat', 10, 0)->nullable();
			$table->decimal('longitude', 10, 0)->nullable();
			$table->string('location')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('zip_codes');
	}

}
