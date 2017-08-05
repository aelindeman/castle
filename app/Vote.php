<?php

namespace Castle;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
	/**
	 * The model's primary key column.
	 *
	 * @var string
	 */
	protected $primaryKey = null;

	/**
	 * Indicates if the IDs are auto-incrementing.
	 *
	 * @var bool
	 */
	public $incrementing = false;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'value',
	];

	/**
	 * The attributes that should be casted to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'value' => 'integer',
	];

	/**
	 * The attributes that should be visible in arrays.
	 *
	 * @var array
	 */
	protected $visible = [
		'value',
		'owner',
		'user',
	];

	// Default method overrides

	/**
	 * Eloquent does not support composite keys for some stupid reason,
	 * so add that behavior here manually.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $query
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	protected function setKeysForSaveQuery(Builder $query)
	{
		return $query->with('owner')
			->where('owner_id', $this->owner->id)
			->where('owner_type', $this->owner->getMorphClass())
			->where('user_id', $this->user_id);
	}

	// Relationships

	/**
	 * @return Relationship
	 */
	public function owner()
	{
		return $this->morphTo();
	}

	/**
	 * @return Relationship
	 */
	public function user()
	{
		return $this->belongsTo(User::class);
	}

	// Query scopes

	/**
	 * @return QueryBuilder
	 */
	public function scopeVoted($query, $user, $owner)
	{
		return $query->where('user_id', $user->id)
			->where('owner_type', $owner->getMorphClass())
			->where('owner_id', $owner->id);
	}

}
