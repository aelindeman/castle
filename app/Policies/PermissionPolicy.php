<?php

namespace Castle\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class PermissionPolicy
{
	use HandlesAuthorization;

	/**
	 * Map models' class names to their permission key.
	 *
	 * Ensure that all models that are bound to this policy in
	 * the AuthServiceProvider container are listed here.
	 *
	 * @var array
	 */
	protected $aliases = [
		\Castle\Attachment::class => 'attachments',
		\Castle\Client::class => 'clients',
		\Castle\Comment::class => 'comments',
		\Castle\Discussion::class => 'discussions',
		\Castle\Document::class => 'docs',
		\Castle\Resource::class => 'resources',
		\Castle\Tag::class => 'tags',
		\Castle\User::class => 'users',
	];

	/**
	 * Handle authorization requests dynamically.
	 *
	 * @return bool
	 */
	public function __call($method, $args)
	{
		list($user, $class) = $args;

		$className = is_object($class) ? get_class($class) : $class;
		$action = $this->aliases[$className].'.'.$method;

		return is_object($class) ?
			($this->hasPermissionOn($user, $action, $class) or $this->hasPermission($user, $action)) :
			$this->hasPermission($user, $action);
	}

	/**
	 * Checks if a user has a permission.
	 *
	 * @return bool
	 */
	protected function hasPermission($user, $permission)
	{
		return $user->permissions->pluck('permission')->contains($permission);
	}

	/**
	 * Check if a user has a permission on a specific object.
	 *
	 * @return bool
	 */
	protected function hasPermissionOn($user, $permission, $entity)
	{
		switch ($permission) {
			case 'users.view':
			case 'users.manage':
				return ($entity instanceOf \Castle\User) and ($user->id == $entity->id);

			case 'discussions.view':
			case 'discussions.manage':
			case 'discussions.participate':
			case 'discussions.delete':
				return ($entity instanceOf \Castle\Discussion) and ($user->id == $entity->created_by);

			case 'comments.view':
			case 'comments.manage':
			case 'comments.participate':
			case 'comments.delete':
				return ($entity instanceOf \Castle\Comment) and ($user->id == $entity->user_id);
		}

		return false;
	}
}
