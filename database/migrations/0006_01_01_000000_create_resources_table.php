<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResourcesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('resource_types', function (Blueprint $table) {
			$table->increments('id');
			$table->string('slug')->unique();
			$table->string('name');
			$table->timestamps();
		});

		Schema::create('resources', function (Blueprint $table) {
			$table->increments('id');
			$table->string('slug');
			$table->string('name');
			$table->integer('resource_type_id');
			$table->string('client_id')->nullable();
			$table->json('metadata')->nullable();
			$table->json('attachments')->nullable();
			$table->timestamps();
			$table->softDeletes();

			$table->unique(['slug', 'client_id']);
			$table->foreign('resource_type_id')->references('id')->on('resource_types');
			$table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
		});

		Schema::create('resources_tags', function (Blueprint $table) {
			$table->integer('resource_id')->unsigned();
			$table->integer('tag_id')->unsigned();

			$table->primary(['resource_id', 'tag_id']);
			$table->foreign('resource_id')->references('id')->on('clients')->onDelete('cascade');
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
		Schema::dropIfExists('resources');
		Schema::dropIfExists('resource_types');
		Schema::dropIfExists('resources_tags');
	}
}
