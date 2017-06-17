<?php

namespace App\Models\Access\User;

use App\Models\Access\User\Traits\Relationship\UserInterestRelationship;

/**
 * Class UserInterest
 *
 * @author Anuj Jaha er.anujjaha@gmail.com
 */

use App\Models\BaseModel;

class UserInterest extends BaseModel
{
    use UserInterestRelationship;
    
    /**
     * Database Table
     *
     */
    protected $table = "user_interests";

    /**
     * Fillable Database Fields
     *
     */
    protected $fillable = [
        'user_id',
        'interest_id'
    ];

    /**
     * Guarded ID Column
     *
     */
    protected $guarded = ["id"];
}