<?php

namespace Castle;

use Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Markdown;
use Nicolaslopezj\Searchable\SearchableTrait as Searchable;

class Client extends Model
{
	use SoftDeletes, Searchable;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name', 'slug', 'color', 'description'
	];

	/**
	 * Searchable rules.
	 *
	 * @var array
	 */
	protected $searchable = [
		'columns' => [
			'clients.name' => 10,
			'clients.slug' => 8,
			'clients.description' => 6,
			'tags.name' => 4,
			'tags.description' => 2,
		],
		'joins' => [
			'clients_tags' => ['clients.id', 'clients_tags.client_id'],
			'tags' => ['clients_tags.tag_id', 'tags.id'],
		]
	];

	// Helper functions

	/**
	 * @return Client
	 */
	public static function findBySlug($slug)
	{
		return self::where('slug', $slug)->first();
	}

	/**
	 * @return string
	 */
	public function toHtml()
	{
		if (Cache::has($this->cacheKey)) {
			$html = Cache::get($this->cacheKey);
		} else {
			$html = Markdown::convertToHtml($this->description);
			Cache::forever($this->cacheKey, $html);
		}

		return $html;
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
	 * @return string
	 */
	public function getCacheKeyAttribute()
	{
		return 'clients.'.$this->slug.'.description.html';
	}

	// Relationships

	/**
	 * @return Relationship
	 */
	public function documents()
	{
		return $this->belongsToMany(Document::class, 'clients_docs', 'client_id', 'doc_id');
	}

	/**
	 * @return Relationship
	 */
	public function resources()
	{
		return $this->hasMany(Resource::class);
	}

	/**
	 * @return Relationship
	 */
	public function tags()
	{
		return $this->belongsToMany(Tag::class, 'clients_tags');
	}

	// Query scopes

	/**
	 * @return QueryBuilder
	 */
	public function scopeNamed($query, $name)
	{
		return $query->where('name', 'like', '%'.$name.'%');
	}

	/**
	 * @return QueryBuilder
	 */
	public function scopeTagged($query, $tag)
	{
		return $query->with('tags')
			->where('tag.name', 'like', '%'.$tag.'%');
	}

}
