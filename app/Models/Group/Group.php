<?php namespace App\Models\Group;

/**
 * Class Group
 *
 * @author Anuj Jaha er.anujjaha@gmail.com
 */

use App\Models\BaseModel;
use App\Models\Group\Traits\Attribute\Attribute;
use App\Models\Group\Traits\Relationship\Relationship;

class Group extends BaseModel
{
    use Attribute, Relationship;
    /**
     * Database Table
     *
     */
    protected $table = "data_groups";

    /**
     * Fillable Database Fields
     *
     */
    protected $fillable = [
        'name',
        'user_id',
        'campus_id',
        'name',
        'description',
        'image',
        'is_private',
        'group_type'
    ];

    /**
     * Guarded ID Column
     *
     */
    protected $guarded = ["id"];


    /**
     * Default Channel
     * 
     * @return object
     */
    public function defaultChannel()
    {
        return $this->group_channels()->first();
    }
}