<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateActionEventsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('action_events', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->char('batch_id', 36);
			$table->integer('user_id')->unsigned()->index();
			$table->string('name');
			$table->string('actionable_type');
			$table->integer('actionable_id')->unsigned();
			$table->string('target_type');
			$table->integer('target_id')->unsigned();
			$table->string('model_type');
			$table->integer('model_id')->unsigned()->nullable();
			$table->text('fields', 65535);
			$table->string('status', 25)->default('running');
			$table->text('exception', 65535);
			$table->timestamps();
			$table->index(['batch_id','model_type','model_id']);
			$table->index(['actionable_type','actionable_id']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('action_events');
	}

}
