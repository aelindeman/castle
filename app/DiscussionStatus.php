<?php

namespace Castle;

use Illuminate\Database\Eloquent\Model;

class DiscussionStatus extends Model
{

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
		'status'
	];

	// Attribute helper functions

	/**
	 * Returns an icon for use by Glyphicons.
	 *
	 * @return string
	 */
	public function getIconAttribute()
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
	public function discussions()
	{
		return $this->hasMany(Discussion::class, 'status_id', 'id');
	}

}
