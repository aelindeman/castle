<?php

use Illuminate\Database\Seeder;
use Castle\Comment;
use Castle\Discussion;
use Castle\User;

class CommentSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$users = User::all();
		$discussions = Discussion::all();

		factory(Comment::class, 30)->make()->each(function($c) use ($users, $discussions) {
			$c->author()->associate($users->random());
			$c->discussion()->associate($discussions->random());
			$c->save();
		});
	}
}
