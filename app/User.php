<?php

namespace Castle;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Collection;
use Nicolaslopezj\Searchable\SearchableTrait as Searchable;

class User extends Authenticatable
{
	use SoftDeletes, Searchable;

	const HISTORY_DEFAULT_MAX_SIZE = 5;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'name', 'email', 'phone', 'password', 'preferences'
	];

	/**
	 * The attributes that should be mutated to dates.
	 *
	 * @var array
	 */
	protected $dates = ['deleted_at'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password', 'remember_token',
	];

	/**
	 * The attributes that should be casted to native types.
	 *
	 * @var array
	 */
	protected $casts = [
		'preferences' => 'collection',
	];

	/**
	 * Searchable rules.
	 *
	 * @var array
	 */
	protected $searchable = [
		'columns' => [
			'users.name' => 10,
			'users.email' => 5,
		]
	];

	// Attribute helper functions

	/**
	 * @return string
	 */
	public function getUrlAttribute()
	{
		return $this->id;
	}

	/**
	 * Get a user's viewing history.
	 *
	 * @return array
	 */
	public function getHistoryAttribute()
	{
		$history = [];

		if (
			$this->preferences and
			$this->preferences instanceOf Collection and
			$this->preferences->has('history')
		) {
			$history = $this->preferences->get('history');
		}

		foreach ($history as $type => $entries) {

			$key = with(new $type)->getKeyName(); // not always 'id'

			switch ($type) {
				case Client::class:
					$resolved = Client::with('tags')
						->whereIn($key, $entries)
						->get();
					break;
				case Discussion::class:
					$resolved = Discussion::with('tags', 'votes')
						->whereIn($key, $entries)
						->get();
					break;
				case Document::class:
					$resolved = Document::with('clients', 'tags')
						->whereIn($key, $entries)
						->get();
					break;
			}

			$resolved = $resolved->keyBy($key);

			// replace primary key entry values with corresponding object,
			// if it exists
			foreach ($entries as $key => $entry) {
				if ($resolved->has($entry)) {
					$history[$type][$key] = $resolved[$entry];
				} else {
					unset($history[$type][$key]);
				}
			}
		}

		// flip so newest are on top
		foreach ($history as $type => $entries) {
			$history[$type] = array_reverse($entries);
		}

		return $history;
	}

	// Helper functions

	/**
	 * Pushes a model to the user's browsing history preference.
	 *
	 * @param $item Model The model to push to history.
	 * @return array
	 */
	public function pushToHistory($item)
	{
		$maxSize = config(
			'castle.users.history-max-size',
			static::HISTORY_DEFAULT_MAX_SIZE
		);

		$prefs = empty($this->preferences) ?
			collect() :
			$this->preferences;

		$history = $prefs->get('history', []);

		$type = get_class($item); // class name
		$value = $item->getKey(); // primary key

		if (!array_key_exists($type, $history)) {
			$history[$type] = [];
		}

		// if the item is already in the history, remove it and collapse the
		// array, then add to the end as normal
		if (($index = array_search($value, $history[$type])) !== false) {
			unset($history[$type][$index]);
			$history[$type] = array_values($history[$type]);
			reset($history[$type]);
		}

		$history[$type][] = $value;

		// don't let the history grow past the configured size
		foreach ($history as $type => $entries) {
			if (count($entries) > $maxSize) {
				$history[$type] = array_slice(
					$entries,
					($maxSize * -1)
				);
			}
		}

		$prefs->put('history', $history);
		$this->preferences = $prefs;

		return $this;
	}

	// Relationships

	/**
	 * @return Relationship
	 */
	public function permissions()
	{
		return $this->belongsToMany(Permission::class, 'users_permissions');
	}

	/**
	 * @return Relationship
	 */
	public function oauthTokens()
	{
		return $this->hasMany(OAuthToken::class);
	}

	/**
	 * @return Relationship
	 */
	public function documents()
	{
		return $this->hasMany(Document::class, 'created_by');
	}

	/**
	 * @return Relationship
	 */
	public function documentEdits()
	{
		return $this->hasMany(Document::class, 'updated_by');
	}

	/**
	 * @return Relationship
	 */
	public function discussions()
	{
		return $this->hasMany(Discussion::class, 'created_by');
	}

	/**
	 * @return Relationship
	 */
	public function discussionEdits()
	{
		return $this->hasMany(Discussion::class, 'updated_by');
	}

	/**
	 * @return Relationship
	 */
	public function comments()
	{
		return $this->hasMany(Comment::class);
	}

	/**
	 * @return Relationship
	 */
	public function votes()
	{
		return $this->hasMany(Vote::class);
	}

	// Query scopes

	/**
	 * @return QueryBuilder
	 */
	public function scopeNamed($query, $name)
	{
		return $query->where('name', $name);
	}

}
