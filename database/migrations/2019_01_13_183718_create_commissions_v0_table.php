<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCommissionsV0Table extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('commissions_v0', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('margin');
			$table->float('commission', 10, 0);
			$table->string('region');
			$table->string('version', 1)->default('1');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('commissions_v0');
	}

}
