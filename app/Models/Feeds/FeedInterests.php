<?php namespace App\Models\Feeds;

/**
 * Class FeedInterests
 *
 * @author Anuj Jaha er.anujjaha@gmail.com
 */

use App\Models\BaseModel;

class FeedInterests extends BaseModel
{
    /**
     * Database Table
     *
     */
    protected $table = "feeds_interests";

    /**
     * Fillable Database Fields
     *
     */
    protected $fillable = [
        'feed_id',
        'interest_id',
    ];

    /**
     * Guarded ID Column
     *
     */
    protected $guarded = ["id"];
}