<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiscussionsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('discussion_statuses', function (Blueprint $table) {
			$table->increments('id');
			$table->string('status');
		});

		Schema::create('discussions', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name');
			$table->string('slug')->unique();
			$table->text('content')->nullable();
			$table->integer('status_id')->nullable()->default(0);
			$table->json('attachments')->nullable();
			$table->integer('created_by')->unsigned()->nullable();
			$table->integer('updated_by')->unsigned()->nullable();
			$table->timestamps();
			$table->softDeletes();

			$table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
			$table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
			$table->foreign('status_id')->references('id')->on('discussion_status');
		});

		Schema::create('discussions_tags', function (Blueprint $table) {
			$table->integer('discussion_id')->unsigned();
			$table->integer('tag_id')->unsigned();

			$table->primary(['discussion_id', 'tag_id']);
			$table->foreign('discussion_id')->references('id')->on('discussions')->onDelete('cascade');
			$table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('discussions_tags');
		Schema::dropIfExists('discussions');
		Schema::dropIfExists('discussions_statuses');
	}
}
