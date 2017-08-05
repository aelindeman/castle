<?php

namespace Castle;

use Cache;
use Carbon;
use Castle\Behaviors\Attachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Markdown;
use Nicolaslopezj\Searchable\SearchableTrait as Searchable;
use Venturecraft\Revisionable\RevisionableTrait as Revisionable;

class Document extends Model
{
	use Attachable, SoftDeletes, Searchable, Revisionable;

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'docs';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name', 'slug', 'content', 'metadata', 'attachments',
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
			'docs.name' => 10,
			'docs.content' => 5,
			'users.name' => 4,
			'tags.name' => 4,
			'tags.description' => 2,
		],
		'joins' => [
			'users' => ['docs.created_by', 'users.id'],
			'users' => ['docs.updated_by', 'users.id'],
			'docs_tags' => ['docs.id', 'docs_tags.doc_id'],
			'tags' => ['docs_tags.tag_id', 'tags.id'],
		]
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
	 * Whitelist of properties to include in the revision history.
	 *
	 * @var array
	 */
	protected $keepRevisionOf = [
		'name', 'slug', 'content', 'metadata',
	];

	// Helper functions

	/**
	 * @return Document
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
			$html = Markdown::convertToHtml($this->content);
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
	public function getAttachmentDirectoryAttribute()
	{
		return implode('/', [
			'docs',
			$this->slug
		]);
	}

	/**
	 * @return string
	 */
	public function getCacheKeyAttribute()
	{
		return 'docs.'.$this->slug.'.content.html';
	}

	// Relationships

	/**
	 * @return Relationship
	 */
	public function createdBy()
	{
		return $this->belongsTo(User::class, 'created_by');
	}

	/**
	 * @return Relationship
	 */
	public function updatedBy()
	{
		return $this->belongsTo(User::class, 'updated_by');
	}

	/**
	 * @return Relationship
	 */
	public function clients()
	{
		return $this->belongsToMany(Client::class, 'clients_docs', 'doc_id', 'client_id');
	}

	/**
	 * @return Relationship
	 */
	public function tags()
	{
		return $this->belongsToMany(Tag::class, 'docs_tags', 'doc_id', 'tag_id');
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
	public function scopeBy($query, $user)
	{
		$user = ($user instanceOf User) ? $user->id : $user;

		return $query->with('createdBy', 'updatedBy')
			->where('created_by', $user)
			->orWhere('updated_by', $user);
	}

	/**
	 * @return QueryBuilder
	 */
	public function scopeRecentlyCreated($query)
	{
		return $query->where('created_at', '>', Carbon\Carbon::now()->subMonths(2))
			->orderBy('created_at', 'asc');
	}

	/**
	 * @return QueryBuilder
	 */
	public function scopeRecentlyEdited($query)
	{
		return $query->where('updated_at', '>', Carbon\Carbon::now()->subMonths(2))
			->orderBy('updated_at', 'asc');
	}

	/**
	 * @return QueryBuilder
	 */
	public function scopeFilter($query, $filter = null)
	{
		if ($filter) {
			if (strpos($filter, ':') !== false) {

				list($attribute, $search) = explode(':', $filter, 2);
				$search = strtolower(trim($search));

				return (strpos($attribute, '!') === 0 ?
					$query->where($attribute, 'not like', '%'.$search.'%') :
					$query->where($attribute, 'like', '%'.$search.'%'));

			} else {
				return $query->where('name', 'like', '%'.$filter.'%')
					->orWhere('slug', 'like', '%'.$filter.'%')
					->orWhere('content', 'like', '%'.$filter.'%');
			}
		}

		return $query;
	}

}
