<?php

use Illuminate\Database\Seeder;
use Castle\Document;
use Castle\Tag;
use Castle\User;

class DocumentSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$users = User::all();
		$tags = Tag::all();

		factory(Document::class, 10)->make()->each(function($d) use ($users, $tags) {
			$d->createdBy()->associate($users->random());
			$d->save();

			foreach ($tags->random(2) as $tag) {
				$d->tags()->attach($tag);
			}

			if (!$d->attachments->isEmpty()) {
				$d->attachments->each(function($a) {
					Storage::disk('attachments')->put($a, '');
				});
			}
		});
	}
}
