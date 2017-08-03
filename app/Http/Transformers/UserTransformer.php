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

    /**
     * Get All Users
     * 
     * @param object $users
     * @return array
     */
    public function getAllUsers($users = null)
    {
        $response = [];

        if($users)
        {
            foreach($users as $user) 
            {
                if(! $user->user_meta)
                {
                    return false;
                }
                
                $profilePicture = url('/profile-pictures/'.$user->user_meta->profile_picture);
                
                $response[] = [
                    'userId'            => $user->id,
                    'name'              => $user->name,
                    'email'             => $user->email,
                    'campusId'          => (int) $user->user_meta->campus->id,
                    'campusName'        => $user->user_meta->campus->name,
                    'profile_picture'   => $profilePicture
                ];
            }
        }

        return $response;
    }

    /**
     * userDetail
     * Single user detail
     * 
     * @param type $data
     * @return type
     */
    public function userDetailWithInterest($user, $userInterests = null) 
    {
        if(! $user->user_meta)
        {
            return false;
        }
        
        $groupDetails = [];
        $profilePicture = url('/profile-pictures/'.$user->user_meta->profile_picture);

        if($user->user_groups)
        {
            $result = [];
        
            $sr = 0;
            foreach($user->user_groups as $group)        
            {
                if(! isset($group->user->user_meta))
                    continue;
                
                $groupImage             =  url('/groups/'.$group->image);
                $creatorProfilePicture  =  url('/profile-pictures/'.$group->user->user_meta->profile_picture);   

                $loginUserId    =  access()->user()->id;
                $isLeader       = ($group->user->id == $loginUserId) ? 1 : 0;
                $isMember       = 0;

                $result[$sr] = [
                    'groupId'           => (int) $group->id,
                    'groupName'         => $group->name,
                    'groupDescription'  => $group->description,
                    'groupImage'        => $groupImage,
                    'isPrivate'         => $group->is_private,
                    'isDiscovery'       => $group->group_type,
                    'isMember'          => 0,
                    'isLeader'          => $isLeader,
                    'interests'         => [],
                    'groupCampus'       => [
                        'campusId'      => (int) $group->campus->id,
                        'campusName'    => $group->campus->name,
                        'campusCode'    => $group->campus->campus_code,
                    ],
                    'groupCreator'      => [
                        'userId'            => (int) $group->user->id,
                        'name'              => $group->user->name,
                        'email'             => $group->user->email,
                        'campusId'          => $group->user->user_meta->campus->id,
                        'campusName'        => $group->user->user_meta->campus->name,
                        'profile_picture'   => $creatorProfilePicture
                    ],
                ];

                if($group->group_interests && count($group->group_interests))
                {
                    foreach($group->group_interests as $interest)   
                    {
                        if(isset($interest) && $interest->image && file_exists(base_path() . '/public/interests/'.$interest->image))
                        {
                            $image = url('/interests/'.$interest->image);
                        }
                        else
                        {
                            $image = url('/interests/default.png');    
                        }

                        $result[$sr]['interests'][] = [
                            'interestId'        => (int) $interest->id,
                            'name'              => $interest->name,
                            'image'             => $image
                        ];
                    }
                }

                $groupLeaders = $group->getLeaders()->pluck('id')->toArray();
                
                if($group->group_members)
                {
                    foreach($group->group_members as $groupMember) 
                    {
                        if($groupMember->user_meta)
                        {
                            $profilePicture = url('/profile-pictures/'.$groupMember->user_meta->profile_picture);
                            $leader         = 0;
                            $member         = 1;

                            if(in_array($groupMember->id, $groupLeaders))
                            {
                                $leader = 1;
                                $member = 0;
                            }

                            if($loginUserId == $groupMember->id)
                            {
                                $isMember = 1;

                                if($isLeader == 0 )
                                {
                                    $isLeader = $leader;
                                }
                            }

                            $result[$sr]['group_members'][] =   [
                                'userId'            => (int) $groupMember->id,
                                'name'              => $groupMember->name,
                                'email'             => $groupMember->email,
                                'campusId'          => $groupMember->user_meta->campus->id,
                                'campusName'        => $groupMember->user_meta->campus->name,
                                'isLeader'          => $leader,
                                'isMember'          => $member,
                                'memberStatus'      => $groupMember->status,
                                'profile_picture'   => $profilePicture
                            ];

                        }
                    }
                }

                $result[$sr]['isMember'] = $isMember;
                $result[$sr]['isLeader'] = $isLeader;
                    
                $sr++;
            }
        }
        
        return [
            'userId'            => $user->id,
            'name'              => $user->name,
            'email'             => $user->email,
            'campusId'          => $user->user_meta->campus->id,
            'campusName'        => $user->user_meta->campus->name,
            'profile_picture'   => $profilePicture,
            'interests'         => isset($userInterests) ? $userInterests : [],
            'userGroups'        => $result
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
