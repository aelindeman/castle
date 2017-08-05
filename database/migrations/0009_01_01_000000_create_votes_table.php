<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVotesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('votes', function (Blueprint $table) {
			// $table->increments('id');
			$table->morphs('owner');
			$table->integer('user_id');
			$table->enum('value', [-1, 0, 1]);
			$table->timestamps();

			$table->primary(['owner_type', 'owner_id', 'user_id']);
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('votes');
	}
}
