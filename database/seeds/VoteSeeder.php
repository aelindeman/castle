<?php

use Illuminate\Database\Seeder;
use Castle\Comment;
use Castle\Discussion;
use Castle\User;
use Castle\Vote;

class VoteSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$users = User::all();

		$owners = collect([
			Comment::all(),
			Discussion::all(),
		]);

		factory(Vote::class, 25)->make()->each(function($v) use ($users, $owners) {
			$giveUp = 10;

			do {
				$user = $users->random();
				$random = $owners->random()->random();

				$exists = $v->voted($user, $random)->exists();
			} while ($exists and (-- $giveUp) > 0);

			if ($giveUp > 0) {
				$v->user()->associate($user);
				$v->owner()->associate($random);

				$v->save();
			}
		});
	}
}
