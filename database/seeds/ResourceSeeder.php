<?php

use Illuminate\Database\Seeder;
use Castle\Client;
use Castle\Resource;
use Castle\ResourceType;
use Castle\Tag;

class ResourceSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$types = ResourceType::all();
		$client = Client::all();
		$tags = Tag::all();

		factory(Resource::class, 20)->make()->each(function($r) use ($types, $client, $tags) {
			$r->type()->associate($types->random());
			$r->client()->associate($client->random());
			$r->save();

			foreach ($tags->random(2) as $tag) {
				$r->tags()->attach($tag);
			}

			if (!$r->attachments->isEmpty()) {
				$r->attachments->each(function($a) {
					Storage::disk('attachments')->put($a, '');
				});
			}
		});
	}
}
