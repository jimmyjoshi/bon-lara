<?php

namespace App\Models\Access\User\Traits\Relationship;

use App\Models\Access\User\User;
use App\Models\Campus\Campus;

/**
 * Class UserMetaRelationship
 */
trait UserMetaRelationship
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
    public function campus()
    {
        return $this->hasOne(Campus::class, 'id', 'campus_id');
    }  
}
