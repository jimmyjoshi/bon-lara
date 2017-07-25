<?php namespace App\Models\Feeds;

/**
 * Class Feeds
 *
 * @author Anuj Jaha er.anujjaha@gmail.com
 */

use App\Models\BaseModel;
use App\Models\Feeds\Traits\Attribute\Attribute;
use App\Models\Feeds\Traits\Relationship\Relationship;

class Feeds extends BaseModel
{
    use Attribute, Relationship;
    /**
     * Database Table
     *
     */
    protected $table = "data_feeds";

    /**
     * Fillable Database Fields
     *
     */
    protected $fillable = [
        'user_id',
        'campus_id',
        'channel_id',
        'group_id',
        'description',
        'attachment',
        'is_attachment',
        'is_campus_feed'
    ];

    /**
     * Guarded ID Column
     *
     */
    protected $guarded = ["id"];
}