<?php

namespace Castle;

use Illuminate\Database\Eloquent\Model;

class ResourceType extends Model
{
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'slug', 'name',
	];

	// Helper functions

	/**
	 * @return ResourceType
	 */
	public static function findBySlug($slug)
	{
		return self::where('slug', $slug)->first();
	}

	// Relationships

	/**
	 * @return Relationship
	 */
	public function resources()
	{
		return $this->hasMany(Resource::class);
	}

	// Query scopes

	/**
	 * @return QueryBuilder
	 */
	public function scopeNamed($query, $name)
	{
		return $query->where('name', 'like', '%'.$name.'%');
	}

}
