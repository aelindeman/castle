<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('clients', function (Blueprint $table) {
			$table->increments('id');
			$table->string('name');
			$table->string('slug')->unique();
			$table->string('color')->nullable();
			$table->text('description')->nullable();
			$table->timestamps();
			$table->softDeletes();
		});

		Schema::create('clients_tags', function (Blueprint $table) {
			$table->integer('tag_id')->unsigned();
			$table->integer('client_id')->unsigned();

			$table->primary(['tag_id', 'client_id']);
			$table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');
			$table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
		});

		Schema::create('clients_docs', function (Blueprint $table) {
			$table->integer('client_id')->unsigned();
			$table->integer('doc_id')->unsigned();

			$table->primary(['client_id', 'doc_id']);
			$table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
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
		Schema::dropIfExists('clients');
		Schema::dropIfExists('clients_tags');
		Schema::dropIfExists('clients_docs');
	}
}
