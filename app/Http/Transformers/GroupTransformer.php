<?php

namespace App\Http\Transformers;

use App\Http\Transformers;
use App\Models\Group\GroupMember;

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
    public function getAllGroupsWithMembers($groups = null, $userInfo = null, $isDiscovery = false)
    {
        $result = [];
    
        if($groups)        
        {
            $grpMember = new GroupMember;
            $sr = 0;
            foreach($groups as $group)        
            {
                if($isDiscovery)
                {
                    if($group->group_type != 1)
                        continue;
                }

                if(! isset($group->user->user_meta))
                    continue;
                
                $groupImage             =  url('/groups/'.$group->image);
                $creatorProfilePicture  =  url('/profile-pictures/'.$group->user->user_meta->profile_picture);   

                $loginUserId    =  access()->user()->id;
                $isLeader       =  0;
                $isMember       =  0;
                $memberStatusObject = $grpMember->select('status')->where(['user_id' => $loginUserId, 'status' => 1])->first();
                $memberStatus   =  isset($memberStatusObject) ? $memberStatusObject->status : 0;

                $result[$sr] = [
                    'groupId'           => (int) $group->id,
                    'groupName'         => $group->name,
                    'groupDescription'  => $group->description,
                    'groupImage'        => $groupImage,
                    'isPrivate'         => (int)$group->is_private,
                    'isDiscovery'       => $group->group_type,
                    'isMember'          => 0,
                    'isLeader'          => $isLeader,
                    'memberStatus'      => $memberStatus,
                    'interests'         => [],
                    'groupLeaderFeeds'  => [],
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


                if($group->get_group_leader_feeds())
                {
                    foreach($group->get_group_leader_feeds()->get() as $groupLeaderFeed)
                    {
                        $creatorProfilePicture  =  url('/profile-pictures/'.$groupLeaderFeed->user->user_meta->profile_picture);
                        $feedAttachment = '';

                        if(isset($feed->is_attachment) && $groupLeaderFeed->is_attachment == 1 && file_exists(base_path() . '/public/feeds/'.$groupLeaderFeed->user_id.'/'.$groupLeaderFeed->attachment))
                        {
                            $feedAttachment = url('/feeds/'.$feed->user_id.'/'.$feed->attachment);
                        }
                        
                        $result[$sr]['groupLeaderFeeds'][] = [
                            'feedId'            => $groupLeaderFeed->id,
                            'description'       => $groupLeaderFeed->description,
                            'is_attachment'     => $groupLeaderFeed->is_attachment,
                            'attachment_link'   => $feedAttachment,
                            'createdAt'         => date('m-d-Y H:i:s', strtotime($groupLeaderFeed->created_at)),
                            'createdDateTime'   => date('m-d-Y', strtotime($groupLeaderFeed->created_at)),
                            'feedCreator'       => [
                                'userId'            => (int) $groupLeaderFeed->user->id,
                                'name'              => $groupLeaderFeed->user->name,
                                'email'             => $groupLeaderFeed->user->email,
                                'campusId'          => $groupLeaderFeed->user->user_meta->campus->id,
                                'campusName'        => $groupLeaderFeed->user->user_meta->campus->name,
                                'profile_picture'   => $creatorProfilePicture
                            ]
                        ];   
                    }
                }

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
                            $memberStatusObject = $grpMember->select('status')->where(['user_id' => $groupMember->id, 'status' => 1])->first();
                            $profilePicture = url('/profile-pictures/'.$groupMember->user_meta->profile_picture);
                            $leader         = 0;

                            if(in_array($groupMember->id, $groupLeaders))
                            {
                                $leader = 1;
                            }

                            if($loginUserId == $groupMember->id)
                            {
                                $isMember = 1;

                                if($isLeader == 0 )
                                {
                                    $isLeader = $leader;
                                }
                            }

                            $mStatus = GroupMember::where(['group_id' => $group->id, 'user_id' => $groupMember->id])->first();
                            $showMemberStatus = $mStatus->status;

                            if(isset($mStatus->is_leader) && $mStatus->is_leader == 1)
                            {
                                $showMemberStatus = 1;                                
                            }

                            $result[$sr]['group_members'][] =   [
                                'userId'            => (int) $groupMember->id,
                                'name'              => $groupMember->name,
                                'email'             => $groupMember->email,
                                'campusId'          => $groupMember->user_meta->campus->id,
                                'campusName'        => $groupMember->user_meta->campus->name,
                                'isLeader'          => $mStatus->is_leader,
                                'isMember'          => 1,
                                'memberStatus'      => $showMemberStatus,
                                'profile_picture'   => $profilePicture
                            ];

                        }
                    }
                }

                // Group Events
                if($group->group_events)
                {
                    foreach($group->group_events as $event)   
                    {
                        $result[$sr]['group_events'][] = [
                            'eventId'           => (int) $event->id,
                            'eventName'         => $event->name,
                            'eventTitle'        => $event->title,
                            'eventStartDate'    => date('m-d-Y H:i:s', strtotime($event->start_date)),
                            'eventEndDate'      => date('m-d-Y H:i:s', strtotime($event->end_date)),
                            'eventCreator'      => [
                                'userId'            => $event->user->id,
                                'name'              => $event->user->name,
                                'email'             => $event->user->email,
                                'campusId'          => $event->user->user_meta->campus->id,
                                'campusName'        => $event->user->user_meta->campus->name,
                                'profile_picture'   => url('/profile-pictures/'.$event->user->user_meta->profile_picture)
                            ],
                        ];
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
     * Get All GroupsWithMembers
     * 
     * @param object $groups
     * @param object $userInfo
     * @return array
     */
    public function getAllGroupsWithMembersRandomFive($groups = null, $userInfo = null)
    {
        $result = [];
    
        if($groups)        
        {
            $groupArr   = $groups->toArray();
            $fiveGroups = array_rand($groupArr, 5);

            $grpMember = new GroupMember;
            $sr = 0;
            foreach($groups as $group)        
            {


                if(! isset($group->user->user_meta))
                    continue;
                
                if($sr > 4 )
                    break;

                $random = rand(111, 999);

                if(($random % 2) == 0)
                {
                    continue;
                }
                
                /*if(! in_array($group->id, $fiveGroups))
                    continue;*/
                
                $groupImage             =  url('/groups/'.$group->image);
                $creatorProfilePicture  =  url('/profile-pictures/'.$group->user->user_meta->profile_picture);   

                $loginUserId    =  access()->user()->id;
                $isLeader       =  0;
                $isMember       =  0;
                $memberStatusObject = $grpMember->select('status')->where(['user_id' => $loginUserId, 'status' => 1])->first();
                $memberStatus   =  isset($memberStatusObject) ? $memberStatusObject->status : 0;

                $result[$sr] = [
                    'groupId'           => (int) $group->id,
                    'groupName'         => $group->name,
                    'groupDescription'  => $group->description,
                    'groupImage'        => $groupImage,
                    'isPrivate'         => (int)$group->is_private,
                    'isDiscovery'       => $group->group_type,
                    'isMember'          => 0,
                    'isLeader'          => $isLeader,
                    'memberStatus'      => $memberStatus,
                    'interests'         => [],
                    'groupLeaderFeeds'  => [],
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


                if($group->get_group_leader_feeds())
                {
                    foreach($group->get_group_leader_feeds()->get() as $groupLeaderFeed)
                    {
                        $creatorProfilePicture  =  url('/profile-pictures/'.$groupLeaderFeed->user->user_meta->profile_picture);
                        $feedAttachment = '';

                        if(isset($feed->is_attachment) && $groupLeaderFeed->is_attachment == 1 && file_exists(base_path() . '/public/feeds/'.$groupLeaderFeed->user_id.'/'.$groupLeaderFeed->attachment))
                        {
                            $feedAttachment = url('/feeds/'.$feed->user_id.'/'.$feed->attachment);
                        }
                        
                        $result[$sr]['groupLeaderFeeds'][] = [
                            'feedId'            => $groupLeaderFeed->id,
                            'description'       => $groupLeaderFeed->description,
                            'is_attachment'     => $groupLeaderFeed->is_attachment,
                            'attachment_link'   => $feedAttachment,
                            'createdAt'         => date('m-d-Y H:i:s', strtotime($groupLeaderFeed->created_at)),
                            'createdDateTime'   => date('m-d-Y', strtotime($groupLeaderFeed->created_at)),
                            'feedCreator'       => [
                                'userId'            => (int) $groupLeaderFeed->user->id,
                                'name'              => $groupLeaderFeed->user->name,
                                'email'             => $groupLeaderFeed->user->email,
                                'campusId'          => $groupLeaderFeed->user->user_meta->campus->id,
                                'campusName'        => $groupLeaderFeed->user->user_meta->campus->name,
                                'profile_picture'   => $creatorProfilePicture
                            ]
                        ];   
                    }
                }

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
                            $memberStatusObject = $grpMember->select('status')->where(['user_id' => $groupMember->id, 'status' => 1])->first();
                            $profilePicture = url('/profile-pictures/'.$groupMember->user_meta->profile_picture);
                            $leader         = 0;

                            if(in_array($groupMember->id, $groupLeaders))
                            {
                                $leader = 1;
                            }

                            if($loginUserId == $groupMember->id)
                            {
                                $isMember = 1;

                                if($isLeader == 0 )
                                {
                                    $isLeader = $leader;
                                }
                            }

                            $mStatus = GroupMember::where(['group_id' => $group->id, 'user_id' => $groupMember->id])->first();
                            $showMemberStatus = $mStatus->status;

                            if(isset($mStatus->is_leader) && $mStatus->is_leader == 1)
                            {
                                $showMemberStatus = 1;                                
                            }

                            $result[$sr]['group_members'][] =   [
                                'userId'            => (int) $groupMember->id,
                                'name'              => $groupMember->name,
                                'email'             => $groupMember->email,
                                'campusId'          => $groupMember->user_meta->campus->id,
                                'campusName'        => $groupMember->user_meta->campus->name,
                                'isLeader'          => $mStatus->is_leader,
                                'memberStatus'      => $showMemberStatus,
                                'profile_picture'   => $profilePicture
                            ];

                        }
                    }
                }

                // Group Events
                if($group->group_events)
                {
                    foreach($group->group_events as $event)   
                    {
                        $result[$sr]['group_events'][] = [
                            'eventId'           => (int) $event->id,
                            'eventName'         => $event->name,
                            'eventTitle'        => $event->title,
                            'eventStartDate'    => date('m-d-Y H:i:s', strtotime($event->start_date)),
                            'eventEndDate'      => date('m-d-Y H:i:s', strtotime($event->end_date)),
                            'eventCreator'      => [
                                'userId'            => $event->user->id,
                                'name'              => $event->user->name,
                                'email'             => $event->user->email,
                                'campusId'          => $event->user->user_meta->campus->id,
                                'campusName'        => $event->user->user_meta->campus->name,
                                'profile_picture'   => url('/profile-pictures/'.$event->user->user_meta->profile_picture)
                            ],
                        ];
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
        $grpMember              = new GroupMember;

        $result = [
            'groupId'           => (int) $group->id,
            'groupName'         => $group->name,
            'groupDescription'  => $group->description,
            'groupImage'        => $groupImage,
            'isPrivate'         => (int)$group->is_private,
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

        // Group Events
        if($group->group_events)
        {
            foreach($group->group_events as $event)   
            {
                $result['group_events'][] = [
                    'eventId'           => (int) $event->id,
                    'eventName'         => $event->name,
                    'eventTitle'        => $event->title,
                    'eventStartDate'    => date('m-d-Y H:i:s', strtotime($event->start_date)),
                    'eventEndDate'      => date('m-d-Y H:i:s', strtotime($event->end_date)),
                    'eventCreator'      => [
                        'userId'            => $event->user->id,
                        'name'              => $event->user->name,
                        'email'             => $event->user->email,
                        'campusId'          => $event->user->user_meta->campus->id,
                        'campusName'        => $event->user->user_meta->campus->name,
                        'profile_picture'   => url('/profile-pictures/'.$event->user->user_meta->profile_picture)
                    ],
                ];
            }
        }


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
                    $leader         = 0;
                    $member         = 1;

                    $memberStatusObject = $grpMember->select('status')->where(['user_id' => $groupMember->id, 'status' => 1])->first();
                    $memberStatus       =  isset($memberStatusObject) ? $memberStatusObject->status : 0;

                    if(in_array($groupMember->id, $groupLeaders))
                    {
                        $leader = 1;
                    }

                    if($leader == 1)
                    {
                        $member = 0;
                    }

                    $mStatus = GroupMember::where(['group_id' => $group->id, 'user_id' => $groupMember->id])->first();
                    $showMemberStatus = $mStatus->status;
                    
                    if(isset($mStatus->is_leader) && $mStatus->is_leader == 1)
                    {
                        $showMemberStatus = 1;                                
                    }


                    $result['group_members'][$sr] =   [
                        'userId'            => (int) $groupMember->id,
                        'name'              => $groupMember->name,
                        'email'             => $groupMember->email,
                        'campusId'          => $groupMember->user_meta->campus->id,
                        'campusName'        => $groupMember->user_meta->campus->name,
                        'isLeader'          => $mStatus->is_leader,
                        'isMember'          => $member,
                        'memberStatus'      => $showMemberStatus,
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
                if(!$member->user_meta)
                    continue;

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
            if(!$member->user_meta)
                    continue;
                
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

    public function getMemberSuggestions($group = null, $allMembers = null)
    {
        $result = [];

        $groupMembers = $group->get_only_group_members()->pluck('id')->toArray();
        $groupLeaders = $group->getLeaders()->pluck('id')->toArray();

        foreach($allMembers as $member)
        {
            $creatorProfilePicture  =  url('/profile-pictures/'.$member->user_meta->profile_picture);   
            $isLeader = $isMember   = 0;

            if(in_array($member->id, $groupMembers))
            {
                $isMember = 1;                  
            }

            if(in_array($member->id, $groupLeaders))
            {
                $isLeader = 1;                  
            }

            $result[] = [
                'userId'            => (int) $member->id,
                'name'              => $member->name,
                'email'             => $member->email,
                'campusId'          => $member->user_meta->campus->id,
                'campusName'        => $member->user_meta->campus->name,
                'isMember'          => $isMember,
                'isLeader'          => $isLeader,
                'profile_picture'   => $creatorProfilePicture
            ];
        }

        return $result;
    }
}

