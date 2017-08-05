<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiscussionCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->increments('id');
            $table->text('content');
            $table->integer('parent')->unsigned()->nullable();
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('discussion_id')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('parent')->references('id')->on('comments')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('discussion_id')->references('id')->on('discussions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments');
    }
}
