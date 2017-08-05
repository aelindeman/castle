<?php

use Illuminate\Database\Seeder;
use Castle\Tag;

class TagSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		factory(Tag::class, 10)->create();
	}
}
