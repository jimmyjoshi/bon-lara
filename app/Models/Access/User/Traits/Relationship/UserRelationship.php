<?php

namespace App\Models\Access\User\Traits\Relationship;


use App\Models\Group\Group;
use App\Models\Event\Event;
use App\Models\System\Session;
use App\Models\Access\User\SocialLogin;
use App\Models\Access\User\UserMeta;
use App\Models\Access\User\UserToken;
use App\Models\Access\User\UserInterest;
use App\Models\Access\User\User;
use App\Models\Interest\Interest;

/**
 * Class UserRelationship.
 */
trait UserRelationship
{
    /**
     * Many-to-Many relations with Role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(config('access.role'), config('access.role_user_table'), 'user_id', 'role_id');
    }

    /**
     * @return mixed
     */
    public function providers()
    {
        return $this->hasMany(SocialLogin::class);
    }

    /**
     * @return mixed
     */
    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    /**
     * @return mixed
     */
    public function events()
    {
        return $this->hasMany(Event::class);
    }    

    /**
     * @return mixed
     */
    public function user_meta()
    {
        return $this->hasOne(UserMeta::class);
    }   

    /**
     * @return mixed
     */
    public function user_token()
    {
        return $this->hasOne(UserToken::class);
    }    

    /**
     * @return mixed
     */
    public function user_interests()
    {
        return $this->hasMany(UserInterest::class);
    } 

    /**
     * @return mixed
     */
    public function user_groups()
    {
        return $this->hasMany(Group::class);
    } 
}
