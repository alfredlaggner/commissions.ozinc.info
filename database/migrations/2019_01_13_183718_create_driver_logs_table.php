<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDriverLogsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('driver_logs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('saleinvoice_id')->nullable();
			$table->integer('driver_id')->nullable();
			$table->integer('vehicle_id')->nullable();
			$table->integer('salesperson_id')->nullable();
			$table->integer('customer_id')->nullable();
			$table->date('delivery_date')->nullable();
			$table->timestamp('delivered_at')->nullable()->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->integer('complaint_id')->nullable();
			$table->date('order_date')->nullable();
			$table->integer('notes_id')->nullable();
			$table->text('notes', 65535)->nullable();
			$table->boolean('is_updated')->nullable()->default(0);
			$table->timestamps();
			$table->text('extra_notes', 65535)->nullable();
			$table->dateTime('delivery_time')->nullable();
			$table->boolean('is_finalized')->nullable();
			$table->date('finalized_date')->nullable();
			$table->time('finalized_time')->nullable();
			$table->integer('collected')->nullable();
			$table->integer('total')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('driver_logs');
	}

}
