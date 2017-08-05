<?php

use Illuminate\Database\Seeder;
use Castle\Client;
use Castle\Document;
use Castle\Tag;

class ClientSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$tags = Tag::all();
		factory(Client::class, 5)->create()->each(function($c) use ($tags) {
			$c->tags()->attach($tags->random());
		});

		$clients = Client::all();
		Document::all()->each(function($d) use ($clients) {
			$d->clients()->attach($clients->random());
		});
	}
}
