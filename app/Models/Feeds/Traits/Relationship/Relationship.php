<?php namespace App\Models\Feeds\Traits\Relationship;

use App\Models\Access\User\User;
use App\Models\Group\Group;
use App\Models\Channel\Channel;
use App\Models\Campus\Campus;
use App\Models\Interest\Interest;

trait Relationship
{
	/**
	 * Relationship Mapping for Account
	 * 
	 * @return mixed
	 */
	public function user()
	{
	    return $this->belongsTo(User::class, 'user_id');
	}

	/**
	 * Relationship Mapping for Campus
	 * 
	 * @return mixed
	 */
	public function channel()
	{
	    return $this->belongsTo(Channel::class, 'channel_id');
	}

	/**
	 * Relationship Mapping for Campus
	 * 
	 * @return mixed
	 */
	public function group()
	{
	    return $this->belongsTo(Group::class, 'group_id');
	}
	

	/**
	 * Relationship Mapping for Campus
	 * 
	 * @return mixed
	 */
	public function campus()
	{
	    return $this->belongsTo(Campus::class, 'campus_id');
	}

	/**
	 * Feed Interests
	 * 
	 * @return mixed
	 */
	public function feed_interests()
	{
	    return $this->belongsToMany(Interest::class, 'feeds_interests', 'feed_id', 'interest_id');
	}
}