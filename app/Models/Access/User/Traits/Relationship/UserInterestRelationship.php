<?php

namespace App\Models\Access\User\Traits\Relationship;

use App\Models\Access\User\User;
use App\Models\Interest\Interest;

/**
 * Class UserInterestRelationship
 */
trait UserInterestRelationship
{
    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }    

    /**
     * @return mixed
     */
    public function interests()
    {
        return $this->belongsTo(Interest::class);
    }        
}
