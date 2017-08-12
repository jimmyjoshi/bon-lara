<?php namespace App\Models\Group;

/**
 * Class GroupMember
 *
 * @author Anuj Jaha er.anujjaha@gmail.com
 */

use App\Models\BaseModel;

class GroupMember extends BaseModel
{

    /**
     * Database Table
     *
     */
    protected $table = "data_group_members";

    /**
     * Fillable Database Fields
     *
     */
    protected $fillable = [
        'group_id',
        'user_id',
        'campus_id',
        'is_leader',
        'status'
    ];

    /**
     * Guarded ID Column
     *
     */
    protected $guarded = ["id"];

    public $timestamps = true;
}