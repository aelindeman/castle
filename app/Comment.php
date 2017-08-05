<?php

namespace Castle;

use Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Markdown;
use Nicolaslopezj\Searchable\SearchableTrait as Searchable;

class Comment extends Model
{
	use SoftDeletes, Searchable;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'content'
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
			'comments.content' => 6,
		],
	];

	// Helper functions

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
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getScoreAttribute()
	{
		return $this->votes->sum('value');
	}

	/**
	 * @return Collection
	 */
	public function getVotersAttribute($query)
	{
		return $this->votes->pluck('value', 'user_id');
	}

	/**
	 * @return string
	 */
	public function getCacheKeyAttribute()
	{
		return 'comments.'.$this->id.'.html';
	}

	// Relationships

	/**
	 * @return Relationship
	 */
	public function discussion()
	{
		return $this->belongsTo(Discussion::class)->withTrashed();
	}

	/**
	 * @return Relationship
	 */
	public function parent()
	{
		return $this->belongsTo(self::class, 'user_id', 'id');
	}

	/**
	 * @return Relationship
	 */
	public function author()
	{
		return $this->belongsTo(User::class, 'user_id', 'id');
	}

	/**
	 * @return Relationship
	 */
	public function votes()
	{
		return $this->morphMany(Vote::class, 'owner');
	}

}
