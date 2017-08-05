<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('docs', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name');
			$table->string('slug')->unique();
			$table->text('content')->nullable();
			$table->json('metadata')->nullable();
			$table->json('attachments')->nullable();
			$table->integer('created_by')->unsigned()->nullable();
			$table->integer('updated_by')->unsigned()->nullable();
			$table->timestamps();
			$table->softDeletes();

			$table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
			$table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
		});

		Schema::create('docs_tags', function (Blueprint $table) {
			$table->integer('tag_id')->unsigned();
			$table->integer('doc_id')->unsigned();

			$table->primary(['tag_id', 'doc_id']);
			$table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');
			$table->foreign('doc_id')->references('id')->on('docs')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('documents');
		Schema::dropIfExists('docs_tags');
	}
}
