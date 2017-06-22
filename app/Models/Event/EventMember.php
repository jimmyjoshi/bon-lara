<?php namespace App\Models\Event;

/**
 * Class EventMember
 *
 * @author Anuj Jaha er.anujjaha@gmail.com
 */

use App\Models\BaseModel;
use App\Models\Event\Traits\Relationship\EventMemberRelationship;

class EventMember extends BaseModel
{
    use EventMemberRelationship;

    /**
     * Database Table
     *
     */
    protected $table = "event_members";

    /**
     * Fillable Database Fields
     *
     */
    protected $fillable = [
        'event_id',
        'user_id'
    ];

    /**
     * Guarded ID Column
     *
     */
    protected $guarded = ["id"];
}