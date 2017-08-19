<?php

namespace App\Models\Access\User;


/**
 * Class UserReport
 *
 * @author Anuj Jaha er.anujjaha@gmail.com
 */

use App\Models\BaseModel;

class UserReport extends BaseModel
{
    /**
     * Database Table
     *
     */
    protected $table = "data_report_users";

    /**
     * Fillable Database Fields
     *
     */
    protected $fillable = [
        'user_id',
        'report_user_id',
        'description'
    ];

    /**
     * Guarded ID Column
     *
     */
    protected $guarded = ["id"];
}