<?php

use Illuminate\Database\Seeder;
use Castle\DiscussionStatus;

class DiscussionStatusSeeder extends Seeder
{
	/**
	 * Default statuses.
	 *
	 * @var array
	 */
	protected $statuses = [
		'New',
		'In progress',
		'Completed',
		'Fixed',
		'Won\'t fix',
		'Can\'t fix',
		'Deferred',
	];

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$existing = DiscussionStatus::all()->pluck('status');

		foreach ($this->statuses as $status) {
			if (!$existing->contains($status)) {
				DiscussionStatus::create(['status' => $status]);
			}
		}
	}
}
