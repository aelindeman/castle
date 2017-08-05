<?php

use Illuminate\Database\Seeder;
use Castle\Permission;

class PermissionSeeder extends Seeder
{
	/**
	 * Default permissions.
	 *
	 * @var array
	 */
	protected $permissions = [
		'users.view',       'users.manage',       'users.delete',
		'docs.view',        'docs.manage',        'docs.delete',
		'clients.view',     'clients.manage',     'clients.delete',
		'resources.view',   'resources.manage',   'resources.delete',
		'attachments.view', 'attachments.manage', 'attachments.delete',
		'tags.view',        'tags.manage',        'tags.delete',
		'discussions.view', 'discussions.manage', 'discussions.delete', 'discussions.participate',
		'comments.view',    'comments.manage',    'comments.delete',    'comments.participate',
	];

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$existing = Permission::all()->pluck('permission');

		foreach ($this->permissions as $permission) {
			if (!$existing->contains($permission)) {
				Permission::create(['permission' => $permission]);
			}
		}
	}
}
