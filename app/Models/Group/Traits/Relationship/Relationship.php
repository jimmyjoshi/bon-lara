<?php namespace App\Models\Group\Traits\Relationship;

use App\Models\Access\User\User;
use App\Models\Group\GroupInterest;
use App\Models\Campus\Campus;
use App\Models\Feeds\Feeds;
use App\Models\Channel\Channel;

use App\Models\Event\Event;

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
	public function campus()
	{
	    return $this->belongsTo(Campus::class, 'campus_id');
	}

	/**
	 * Relationship Mapping for Member
	 * 
	 * @return mixed
	 */
	public function group_members()
	{
		return $this->belongsToMany(User::class, 'data_group_members', 'group_id',  'user_id');
	}

	/**
	 * Relationship Mapping for interest
	 * 
	 * @return mixed
	 */
	public function group_interests()
	{
		return $this->belongsToMany(Interest::class, 'data_group_interests', 'group_id', 'interest_id');
	}

	public function get_group_interests()
	{
		return $this->hasMany(GroupInterest::class);
	}
	
	/**
	 * Get Leaders
	 * 
	 * @return mixed
	 */
	public function getLeaders()
	{
	    return $this->group_members()->where(['is_leader' => 1])->get();
	}

	/**
	 * Get Members
	 * 
	 * @return mixed
	 */
	public function get_only_group_members()
	{
	    return $this->group_members()->where(['is_leader' => 0])->get();
	}

	public function get_group_feeds()
	{
		return $this->hasMany(Feeds::class)->where(['is_campus_feed' => 0]);	
	}

	/**
     * @return mixed
     */
    public function group_feeds()
    {
        return $this->hasMany(Feeds::class)->where(['is_campus_feed' => 0])->get();
    } 

    /**
     * @return mixed
     */
    public function group_events()
    {
        return $this->hasMany(Event::class);
    }

    /**
     * @return mixed
     */
    public function group_channels()
    {
        return $this->hasMany(Channel::class);
    }
}
