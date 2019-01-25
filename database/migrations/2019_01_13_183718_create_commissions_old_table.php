<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCommissionsOldTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('commissions_old', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('margin');
			$table->float('commission', 10, 0);
			$table->integer('version');
			$table->string('region');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('commissions_old');
	}

}
