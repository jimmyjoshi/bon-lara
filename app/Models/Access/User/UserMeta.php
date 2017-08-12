<?php

namespace App\Models\Access\User;

use App\Models\Access\User\Traits\Relationship\UserMetaRelationship;
/**
 * Class UserMeta
 *
 * @author Anuj Jaha er.anujjaha@gmail.com
 */

use App\Models\BaseModel;

class UserMeta extends BaseModel
{
    use UserMetaRelationship;
    
    /**
     * Database Table
     *
     */
    protected $table = "user_meta";

    /**
     * Fillable Database Fields
     *
     */
    protected $fillable = [
        'user_id',
        'campus_id',
        'profile_picture'
    ];

    /**
     * Guarded ID Column
     *
     */
    protected $guarded = ["id"];
}