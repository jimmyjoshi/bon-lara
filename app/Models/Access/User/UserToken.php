<?php

namespace App\Models\Access\User;

use App\Models\BaseModel;

/**
 * Class UserToken
 */
class UserToken extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = "data_user_tokens";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'campus_id', 'token'];

    /**
     * Relationship Mapping for Account
     * 
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
