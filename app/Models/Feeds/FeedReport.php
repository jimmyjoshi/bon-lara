<?php namespace App\Models\Feeds;

/**
 * Class FeedReport
 *
 * @author Anuj Jaha er.anujjaha@gmail.com
 */

use App\Models\BaseModel;

class FeedReport extends BaseModel
{
    /**
     * Database Table
     *
     */
    protected $table = "data_report_feeds";

    /**
     * Fillable Database Fields
     *
     */
    protected $fillable = [
        'user_id',
        'feed_id',
        'description'
    ];

    /**
     * Guarded ID Column
     *
     */
    protected $guarded = ["id"];
}