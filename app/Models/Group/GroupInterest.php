<?php namespace App\Models\Group;

/**
 * Class GroupInterest
 *
 * @author Anuj Jaha er.anujjaha@gmail.com
 */

use App\Models\BaseModel;
use App\Models\Interest\Interest;

class GroupInterest extends BaseModel
{
    /**
     * Database Table
     *
     */
    protected $table = "data_group_interests";

    /**
     * Fillable Database Fields
     *
     */
    protected $fillable = [
        'group_id',
        'interest_id'
    ];

    /**
     * Guarded ID Column
     *
     */
    protected $guarded = ["id"];

    /**
     * Relationship Mapping for Interest
     * 
     * @return mixed
     */
    public function interest()
    {
        return $this->belongsTo(Interest::class, 'interest_id');
    }
}