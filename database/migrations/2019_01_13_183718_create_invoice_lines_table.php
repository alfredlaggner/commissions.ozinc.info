<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInvoiceLinesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('invoice_lines', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('invoice_id')->nullable();
			$table->integer('order_id')->nullable();
			$table->string('order_number')->nullable()->default('NULL');
			$table->integer('customer_id')->nullable();
			$table->integer('product_id')->nullable();
			$table->float('product_margin', 10)->nullable();
			$table->integer('ext_id')->nullable()->index('ext_id');
			$table->char('code', 10)->nullable()->default('NULL');
			$table->text('name', 65535)->nullable();
			$table->integer('quantity')->nullable();
			$table->float('cost', 10)->nullable();
			$table->float('unit_price', 10)->nullable();
			$table->float('price_subtotal', 10)->nullable();
			$table->float('price_total', 10)->nullable();
			$table->float('margin', 10, 0)->nullable();
			$table->float('commission')->nullable();
			$table->float('comm_percent', 8, 4)->nullable();
			$table->integer('comm_version')->default(1);
			$table->string('comm_region', 1)->nullable();
			$table->timestamps();
			$table->integer('sales_person_id')->nullable();
			$table->string('line_note_id')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('invoice_lines');
	}

}
