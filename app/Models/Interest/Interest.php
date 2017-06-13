<?php namespace App\Models\Interest;

/**
 * Class Interest
 *
 * @author Anuj Jaha er.anujjaha@gmail.com
 */

use App\Models\BaseModel;
use App\Models\Interest\Traits\Attribute\Attribute;
use App\Models\Interest\Traits\Relationship\Relationship;

class Interest extends BaseModel
{
    use Attribute, Relationship;
    /**
     * Database Table
     *
     */
    protected $table = "all_interests";

    /**
     * Fillable Database Fields
     *
     */
    protected $fillable = [
        'name',
        'image'
    ];

    /**
     * Guarded ID Column
     *
     */
    protected $guarded = ["id"];
}