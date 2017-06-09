<?php

namespace App\Http\Transformers;

use App\Http\Transformers;
use Html;

class UserTransformer extends Transformer 
{
    public function transform($data) 
    {
        return [
            'userId'    => $data->id,
            'userToken' => $data->token,
            'name'      => $this->nulltoBlank($data->name),
            'username'  => $this->nulltoBlank($data->username),
            'email'     => $this->nulltoBlank($data->email)
        ];
    }
    
    public function getUserInfo($data) 
    {
        return [
            'userId'    => $data->id,
            'name'      => $this->nulltoBlank($data->name),
            'email'     => $this->nulltoBlank($data->email)
        ];
    }
    
    /**
     * userDetail
     * Single user detail
     * 
     * @param type $data
     * @return type
     */
    public function userDetail($user) 
    {
        if(! $user->user_meta)
        {
            return false;
        }
        
        $profilePicture =  url('/profile-pictures/'.$user->user_meta->profile_picture);

        return [
            'userId'            => $user->id,
            'name'              => $user->name,
            'email'             => $user->email,
            'campusId'          => $user->user_meta->campus->id,
            'campusName'        => $user->user_meta->campus->name,
            'profile_picture'   => $profilePicture
        ];
    }

    /*
     * User Detail and it's parameters
     */
    public function singleUserDetail($data){        
        return [
            'UserId' => $data['id'],            
            'Name' => $this->nulltoBlank($data['name']),
            'Email' => $this->nulltoBlank($data['email']),
            'MobileNumber' => $this->nulltoBlank($data['mobile_number']),
        ];
    }
    
    public function transformStateCollection(array $items) {
        return array_map([$this, 'getState'], $items);
    }
}
