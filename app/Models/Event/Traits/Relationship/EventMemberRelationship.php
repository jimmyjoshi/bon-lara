<?php namespace App\Models\Event\Traits\Relationship;

use App\Models\Access\User\User;
use App\Models\Event\EventMember;
use App\Models\Event\Event;

trait EventMemberRelationship
{
	/**
	 * Relationship Mapping for Account
	 * @return mixed
	 */
	public function user()
	{
	    return $this->belongsTo(User::class, 'user_id');
	}

	/**
	 * Relationship Mapping for Events
	 * @return mixed
	 */
	public function events()
	{
	    return $this->belongsTo(Event::class, 'event_id');
	}

	/**
	 * Relationship Mapping for EventMember
	 * 
	 * @return mixed
	 */
	
}