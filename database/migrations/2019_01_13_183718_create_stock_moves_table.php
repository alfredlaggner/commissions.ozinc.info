<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStockMovesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('stock_moves', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('ext_id')->nullable();
			$table->date('date')->nullable();
			$table->string('lot_name')->nullable();
			$table->string('reference')->nullable();
			$table->integer('product_id')->unsigned()->nullable();
			$table->string('sku')->nullable()->default('NULL');
			$table->string('name')->nullable();
			$table->string('location')->nullable();
			$table->string('location_dest')->nullable();
			$table->float('qty_done', 10, 0)->nullable();
			$table->char('state')->nullable();
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
		Schema::drop('stock_moves');
	}

}
