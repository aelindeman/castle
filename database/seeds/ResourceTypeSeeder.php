<?php

use Illuminate\Database\Seeder;

class ResourceTypeSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$basicTypes = [
			'cert' => 'Certificate',
			'domain' => 'Domain',
			'password' => 'Password',
		];

		foreach ($basicTypes as $slug => $name) {
			DB::table('resource_types')->insert([
				'slug' => $slug,
				'name' => $name
			]);
		}
	}
}
