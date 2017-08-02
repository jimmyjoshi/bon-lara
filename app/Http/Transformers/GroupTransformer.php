<?php

namespace App\Http\Transformers;

use App\Http\Transformers;

class GroupTransformer extends Transformer 
{
    /**
     * Transform
     * 
     * @param array $data
     * @return array
     */
    public function transform($data) 
    {
        if(is_array($data))
        {
            $data = (object)$data;
        }
        
        $creatorProfilePicture =  url('/profile-pictures/'.$data->user->user_meta->profile_picture);   

        return [
            'eventId'           => (int) $data->id,
            'eventName'         => $data->name,
            'eventTitle'        => $data->title,
            'eventStartDate'    => date('d-m-Y', strtotime($data->start_date)),
            'eventEndDate'      => date('d-m-Y', strtotime($data->end_date)),
                'eventCreator'      => [
                'userId'            => $data->user->id,
                'name'              => $data->user->name,
                'email'             => $data->user->email,
                'campusId'          => $data->user->user_meta->campus->id,
                'campusName'        => $data->user->user_meta->campus->name,
                'profile_picture'   => $creatorProfilePicture
            ],
        ];
    }

    public function createEvent($model = null)
    {
        if(! isset($model->user->user_meta))
            return;

        $creatorProfilePicture =  url('/profile-pictures/'.$model->user->user_meta->profile_picture);

        return [
            'eventId'           => (int) $model->id,
            'eventName'         => $model->name,
            'eventTitle'        => $model->title,
            'eventStartDate'    => date('d-m-Y', strtotime($model->start_date)),
            'eventEndDate'      => date('d-m-Y', strtotime($model->end_date)),
            'eventCreator'      => [
                'userId'            => $model->user->id,
                'name'              => $model->user->name,
                'email'             => $model->user->email,
                'campusId'          => $model->user->user_meta->campus->id,
                'campusName'        => $model->user->user_meta->campus->name,
                'profile_picture'   => $creatorProfilePicture
            ],
        ];
    }

    /**
     * Get All Events
     * 
     * @param Object $events
     * @param Object $user
     * @return array
     */
    public function getAllEvents($events = null, $userInfo = null)
    {
        $result = [];

        if($events)
        {
            $sr = 0;

            foreach($events as $event)
            {
                if(! isset($event->user->user_meta))
                    continue;

                $creatorProfilePicture =  url('/profile-pictures/'.$event->user->user_meta->profile_picture);
                    
                $result[$sr] = [
                    'eventId'           => (int) $event->id,
                    'eventName'         => $event->name,
                    'eventTitle'        => $event->title,
                    'eventStartDate'    => $event->start_date,
                    'eventEndDate'      => $event->end_date,
                    'joinEvent'         => false,
                    'eventCreator'      => [
                        'userId'            => (int) $event->user->id,
                        'name'              => $event->user->name,
                        'email'             => $event->user->email,
                        'campusId'          => $event->user->user_meta->campus->id,
                        'campusName'        => $event->user->user_meta->campus->name,
                        'profile_picture'   => $creatorProfilePicture
                    ],
                ];

                foreach($event->event_members as $user)
                {
                    if($userInfo->id == $user->id)
                    {
                        $result[$sr]['joinEvent'] = true;
                    }
                    
                    $profilePicture =  url('/profile-pictures/'.$user->user_meta->profile_picture);

                    $result[$sr]['event_members'][] =   [
                            'userId'            => (int) $user->id,
                            'name'              => $user->name,
                            'email'             => $user->email,
                            'campusId'          => $user->user_meta->campus->id,
                            'campusName'        => $user->user_meta->campus->name,
                            'profile_picture'   => $profilePicture
                        ];
                }   
                $sr++;
            }
        }
        
        return $result;
    }

    /**
     * Get All GroupsWithMembers
     * 
     * @param object $groups
     * @param object $userInfo
     * @return array
     */
    public function getAllGroupsWithMembers($groups = null, $userInfo = null)
    {
        $result = [];
    
        if($groups)        
        {
            $sr = 0;
            foreach($groups as $group)        
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
                            $leader         = 1;

                            if(in_array($groupMember->id, $groupLeaders))
                            {
                                $leader = 0;
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

        return $result;
    }

    /**
     * Get Single Group
     * 
     * @param object $group
     * @param object $userInfo
     * @return array
     */
    public function getSingleGroup($group = null, $userInfo = null)
    {
        $result                 = [];
        $groupImage             =  url('/groups/'.$group->image);
        $creatorProfilePicture  =  url('/profile-pictures/'.$group->user->user_meta->profile_picture);   

        $result = [
            'groupId'           => (int) $group->id,
            'groupName'         => $group->name,
            'groupDescription'  => $group->description,
            'groupImage'        => $groupImage,
            'isPrivate'         => $group->is_private,
            'isDiscovery'       => $group->group_type,
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

        if($group->get_group_interests && count($group->get_group_interests))
        {
            $sr = 0;
            foreach($group->get_group_interests as $interest)   
            {
                if(! $interest->interest)
                {
                    continue;
                }
                if(isset($interest->interest) && $interest->interest->image && file_exists(base_path() . '/public/interests/'.$interest->interest))
                {
                    $image = url('/interests/'.$interest->interest->image);
                }
                else
                {
                    $image = url('/interests/default.png');    
                }
                
                $result['interests'][$sr] = [
                    'interestId'        => $interest->interest->id,
                    'name'              => $interest->interest->name,
                    'image'             => $image
                ];

                $sr++;
            }
        }

        $groupLeaders = $group->getLeaders()->pluck('id')->toArray();
        if($group->group_members)
        {
            $sr = 0;
            foreach($group->group_members as $groupMember) 
            {
                if($groupMember->user_meta)
                {
                    $profilePicture = url('/profile-pictures/'.$groupMember->user_meta->profile_picture);
                    $leader         = 1;

                    if(in_array($groupMember->id, $groupLeaders))
                    {
                        $leader = 0;
                    }
                    $result['group_members'][$sr] =   [
                        'userId'            => (int) $groupMember->id,
                        'name'              => $groupMember->name,
                        'email'             => $groupMember->email,
                        'campusId'          => $groupMember->user_meta->campus->id,
                        'campusName'        => $groupMember->user_meta->campus->name,
                        'isLeader'          => $leader,
                        'memberStatus'      => $groupMember->status,
                        'profile_picture'   => $profilePicture
                    ];
                }
                $sr++;
            }
        }

    return $result;
    }

    public function getGroupMembers($group)
    {
        $result = [];

        if($group->get_only_group_members())
        {
            $sr = 0;
            foreach($group->get_only_group_members() as $member)   
            {
                $creatorProfilePicture =  url('/profile-pictures/'.$member->user_meta->profile_picture);   

                $result[$sr] = [
                    'userId'            => $member->id,
                    'name'              => $member->name,
                    'email'             => $member->email,
                    'profile_picture'   => $creatorProfilePicture,
                    'isLeader'          => 0
                ];    
                $sr++;
            }
        }


        foreach($group->get_group_leaders()->get() as $member)   
        {
            $creatorProfilePicture =  url('/profile-pictures/'.$member->user_meta->profile_picture);   

            $result[$sr] = [
                'userId'            => $member->id,
                'name'              => $member->name,
                'email'             => $member->email,
                'profile_picture'   => $creatorProfilePicture,
                'isLeader'          => 1
            ];

            $sr++;   
        }
            
        return $result;
    }
}

