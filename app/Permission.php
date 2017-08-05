<?php

namespace Castle;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
	/**
	 * Type (suffix) of permission to give to new users
	 */
	const DEFAULT_TYPE = 'view';

	/**
	 * Indicates if the model should be timestamped.
	 *
	 * @var bool
	 */
	public $timestamps = false;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'permission'
	];

	// Helper functions

	/**
	 * @return Collection
	 */
	public static function defaults()
	{
		return self::byType(self::DEFAULT_TYPE)->get();
	}

	// Attribute helper functions

	/**
	 * @return string
	 */
	public function getGroupAttribute()
	{
		return substr($this->permission, 0, strpos($this->permission, '.'));
	}

	/**
	 * @return string
	 */
	public function getTypeAttribute()
	{
		return substr($this->permission, strrpos($this->permission, '.') + 1);
	}

	/**
	 * Returns an icon for use by Glyphicons.
	 *
	 * @return string
	 */
	public function getTypeIconAttribute()
	{
		return strtr($this->type, [
			'view' => 'eye-open',
			'participate' => 'comment',
			'manage' => 'pencil',
			'delete' => 'trash',
		]);
	}

	// Relationships

	/**
	 * @return Relationship
	 */
	public function users()
	{
		return $this->belongsToMany(User::class, 'users_permissions');
	}

	// Query scopes

	/**
	 * @return QueryBuilder
	 */
	public function scopeByName($query, $name)
	{
		return $query->where('permission', $name)->first();
	}

	/**
	 * @return QueryBuilder
	 */
	public function scopeByCategory($query, $category)
	{
		return $query->where('permission', 'like', $category.'.%');
	}

	/**
	 * @return QueryBuilder
	 */
	public function scopeByType($query, $type)
	{
		return $query->where('permission', 'like', '%.'.$type);
	}
}
