<?php

namespace Castle;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nicolaslopezj\Searchable\SearchableTrait as Searchable;

class Tag extends Model
{
	use SoftDeletes, Searchable;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name', 'slug', 'description', 'color',
	];

	/**
	 * The attributes that should be mutated to dates.
	 *
	 * @var array
	 */
	protected $dates = ['deleted_at'];

	/**
	 * Searchable rules.
	 *
	 * @var array
	 */
	protected $searchable = [
		'columns' => [
			'tags.name' => 10,
			'tags.description' => 7,
		]
	];

	// Helper functions

	/**
	 * @return Tag
	 */
	public static function findBySlug($slug)
	{
		return self::where('slug', $slug)->first();
	}

	// Attribute helper functions

	/**
	 * @return string
	 */
	public function getUrlAttribute()
	{
		return $this->slug;
	}

	/**
	 * @return int
	 */
	public function getOccurencesAttribute()
	{
		return
			$this->clients->count() +
			$this->documents->count() +
			$this->discussions->count() +
			$this->resources->count();
	}

	// Relationships

	/**
	 * @return Relationship
	 */
	public function documents()
	{
		return $this->belongsToMany(Document::class, 'docs_tags', 'tag_id', 'doc_id');
	}

	/**
	 * @return Relationship
	 */
	public function clients()
	{
		return $this->belongsToMany(Client::class, 'clients_tags');
	}

	/**
	 * @return Relationship
	 */
	public function resources()
	{
		return $this->belongsToMany(Resource::class, 'resources_tags');
	}

	/**
	 * @return Relationship
	 */
	public function discussions()
	{
		return $this->belongsToMany(Discussion::class, 'discussions_tags');
	}

	// Query scopes

	/**
	 * @return QueryBuilder
	 */
	public function scopeBySlug($query, $slug)
	{
		return $query->where('slug', 'like', '%'.$slug.'%');
	}

	/**
	 * @return QueryBuilder
	 */
	public function scopeFilter($query, $filter = null)
	{
		if ($filter) {
			if (strpos($filter, ':') !== false) {

				list($attribute, $search) = explode(':', $filter, 2);
				$search = trim($search);

				return (strpos($attribute, '!') === 0 ?
					$query->where($attribute, 'not like', '%'.$search.'%') :
					$query->where($attribute, 'like', '%'.$search.'%'));

			} else {
				return $query->where('name', 'like', '%'.$filter.'%')
					->orWhere('description', 'like', '%'.$filter.'%');
			}
		}

		return $query;
	}

}
