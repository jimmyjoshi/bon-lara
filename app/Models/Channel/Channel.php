<?php namespace App\Models\Channel;

/**
 * Class Channel
 *
 * @author Anuj Jaha er.anujjaha@gmail.com
 */

use App\Models\BaseModel;
use App\Models\Channel\Traits\Attribute\Attribute;
use App\Models\Channel\Traits\Relationship\Relationship;

class Channel extends BaseModel
{
    use Attribute, Relationship;
    /**
     * Database Table
     *
     */
    protected $table = "data_channels";

    /**
     * Fillable Database Fields
     *
     */
    protected $fillable = [
        'name',
        'user_id',
        'group_id',
        'campus_id',
    ];

    /**
     * Guarded ID Column
     *
     */
    protected $guarded = ["id"];
}