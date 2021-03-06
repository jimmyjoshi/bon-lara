<?php

namespace App\Http\Transformers;

use App\Http\Transformers;
use Html;
use App\Models\Group\GroupMember;

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
        
        $groupDetails   = [];
        $grpMember      = new GroupMember;
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

                $grpLeader          = 0;
                $grpMemberValue     = 0;
                $grpMemberStatus    = 0;

                $memberStatusObject = GroupMember::where(['user_id' => $user->id, 'group_id' => $group->id])->first();

                if($memberStatusObject)
                {
                    $grpLeader          = $memberStatusObject->is_leader;
                    $grpMemberValue     = 1;
                    $grpMemberStatus    = $memberStatusObject->status;
                }

                $result[$sr] = [
                    'groupId'           => (int) $group->id,
                    'groupName'         => $group->name,
                    'groupDescription'  => $group->description,
                    'groupImage'        => $groupImage,
                    'isPrivate'         => $group->is_private,
                    'isDiscovery'       => $group->group_type,
                    'isMember'          => $grpMemberValue,
                    'isLeader'          => $grpLeader,
                    'memberStatus'      => $grpMemberStatus,
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
                            $grpLeader          = 0;
                            $grpMemberValue     = 0;
                            $grpMemberStatus    = 0;

                            $memberStatusObject = GroupMember::where(['user_id' => $groupMember->id, 'group_id' => $group->id])->first();

                            if($memberStatusObject)
                            {
                                $grpLeader          = $memberStatusObject->is_leader;
                                $grpMemberValue     = 1;
                                $grpMemberStatus    = $memberStatusObject->status;
                            }

                            $memberStatus   =  isset($memberStatusObject) ? $memberStatusObject->status : 0;

                            $profilePicture = url('/profile-pictures/'.$groupMember->user_meta->profile_picture);
                            
                            $result[$sr]['group_members'][] =   [
                                'userId'            => (int) $groupMember->id,
                                'name'              => $groupMember->name,
                                'email'             => $groupMember->email,
                                'campusId'          => $groupMember->user_meta->campus->id,
                                'campusName'        => $groupMember->user_meta->campus->name,
                                'isLeader'          => $grpLeader,
                                'isMember'          => $grpMemberValue,
                                'memberStatus'      => $grpMemberStatus,
                                'profile_picture'   => $profilePicture
                            ];

                        }
                    }
                }

                /*$result[$sr]['isMember'] = $isMember;
                $result[$sr]['isLeader'] = $isLeader;*/
                    
                $sr++;
            }
        }
        
        return [
            'userId'            => $user->id,
            'name'              => $user->name,
            'email'             => $user->email,
            'campusId'          => $user->user_meta->campus->id,
            'campusName'        => $user->user_meta->campus->name,
            'profile_picture'   => url('/profile-pictures/'.$user->user_meta->profile_picture),
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