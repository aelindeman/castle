<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePermissionsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('permissions', function (Blueprint $table) {
			$table->increments('id');
			$table->string('permission')->unique();
		});

		Schema::create('users_permissions', function (Blueprint $table) {
			$table->integer('user_id')->unsigned();
			$table->integer('permission_id')->unsigned();

			$table->primary(['user_id', 'permission_id']);
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
			$table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('permissions');
		Schema::dropIfExists('users_permissions');
	}
}
