<?php namespace App\Models\Campus;

/**
 * Class Campus
 *
 * @author Anuj Jaha er.anujjaha@gmail.com
 */

use App\Models\BaseModel;
use App\Models\Campus\Traits\Attribute\Attribute;
use App\Models\Campus\Traits\Relationship\Relationship;

class Campus extends BaseModel
{
    use Attribute, Relationship;
    /**
     * Database Table
     *
     */
    protected $table = "all_campus";

    /**
     * Fillable Database Fields
     *
     */
    protected $fillable = [
        'name',
        'campus_code',
        'valid_domain',
        'contact_person_name',
        'contact_number',
        'email_id'
    ];

    /**
     * Guarded ID Column
     *
     */
    protected $guarded = ["id"];
}