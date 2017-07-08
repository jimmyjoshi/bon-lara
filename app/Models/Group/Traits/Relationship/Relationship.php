<?php namespace App\Models\Group\Traits\Relationship;

use App\Models\Access\User\User;
use App\Models\Campus\Campus;

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
	 * Relationship Mapping for Campus
	 * 
	 * @return mixed
	 */
	public function group_members()
	{
		return $this->belongsToMany(User::class, 'data_group_members', 'group_id',  'user_id');
	}
	
	public function getLeaders()
	{
	    return $this->group_members()->where(['is_leader' => 1])->get();
	}
}