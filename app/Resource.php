<?php

namespace Castle;

use Cache;
use Castle\Behaviors\Attachable;
use Castle\Behaviors\ChecksForEncryptedMetadata;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Markdown;
use Nicolaslopezj\Searchable\SearchableTrait as Searchable;
use Venturecraft\Revisionable\RevisionableTrait as Revisionable;

class Resource extends Model
{
	use Attachable,
		ChecksForEncryptedMetadata,
		SoftDeletes,
		Searchable,
		Revisionable;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name', 'slug', 'description', 'metadata', 'attachments'
	];

	/**
	 * The attributes that should be mutated to dates.
	 *
	 * @var array
	 */
	protected $dates = ['deleted_at'];

	/**
	 * The attributes that should be casted to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'metadata' => 'collection',
	];

	/**
	 * Searchable rules.
	 *
	 * @var array
	 */
	protected $searchable = [
		'columns' => [
			'resources.name' => 10,
			'resources.description' => 6,
			'resource_types.name' => 5,
			'clients.name' => 7,
			'clients.slug' => 5,
			// 'tags.name' => 4,
			// 'tags.description' => 2,
		],
		'joins' => [
			'clients' => ['resources.client_id','clients.id'],
			'resource_types' => ['resources.resource_type_id','resource_types.id'],
			'resources_tags' => ['resources.id', 'resources_tags.resource_id'],
			'tags' => ['resources_tags.tag_id', 'tags.id'],
		],
	];

	/**
	 * Whether or not this model keeps a revision history.
	 *
	 * @var bool
	 */
	protected $revisionEnabled = true;

	/**
	 * Whether or not to discard old revisions.
	 *
	 * (Only applies when $revisionEnabled is true.)
	 *
	 * @var bool
	 */
	protected $revisionCleanup = false;

	/**
	 * The number of revisions to keep.
	 *
	 * @var int
	 */
	protected $historyLimit = 100;

	/**
	 * Whitelist of properties to include in the revision history.
	 *
	 * @var array
	 */
	protected $keepRevisionOf = [
		'name', 'slug', 'description', 'metadata'
	];

	// Helper functions

	/**
	 * @return Resource
	 */
	public static function findBySlug($client, $slug)
	{
		$client = ($client instanceOf Client) ? $client : Client::findBySlug($client);

		if (!$client) {
			return null;
		}

		return self::where('client_id', $client->id)
			->where('slug', $slug)
			->first();
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
		return 'resources.'.$this->id.'.description.html';
	}

	/**
	 * @return string
	 */
	public function getAttachmentDirectoryAttribute()
	{
		return implode('/', [
			'clients',
			isset($this->client) ? $this->client->url : $this->client,
			'resources',
			$this->url
		]);
	}

	// Relationships

	/**
	 * @return Relationship
	 */
	public function type()
	{
		return $this->belongsTo(ResourceType::class, 'resource_type_id');
	}

	/**
	 * @return Relationship
	 */
	public function tags()
	{
		return $this->belongsToMany(Tag::class, 'resources_tags');
	}

	/**
	 * @return Relationship
	 */
	public function client()
	{
		return $this->belongsTo(Client::class);
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
