<?php

namespace App\Http\Transformers;

use App\Http\Transformers;
use App\Models\Feeds\FeedReport;

class FeedsTransformer extends Transformer 
{
    /**
     * Transform
     * 
     * @param array $data
     * @return array
     */
    public function transform($feed) 
    {
        $groupImage = '';

        if(isset($feed->group))
        {
            $groupImage =  url('/groups/'.$feed->group->image);
        }
        
        $feedAttachment = '';

        if(isset($feed->is_attachment) && $feed->is_attachment == 1 && file_exists(base_path() . '/public/feeds/'.$feed->user_id.'/'.$feed->attachment))
        {
            $feedAttachment = url('/feeds/'.$feed->user_id.'/'.$feed->attachment);
        }

        if(! isset($feed->user->user_meta))
        {
            return false;
        }

        $creatorProfilePicture  =  url('/profile-pictures/'.$feed->user->user_meta->profile_picture);   

        $response = [
            'feedId'            => $feed->id,
            'description'       => $feed->description,
            'is_attachment'     => $feed->is_attachment,
            'createdAt'         => date('m-d-Y H:i:s', strtotime($feed->created_at)),
            'feedCreator'       => [
                'userId'            => (int) $feed->user->id,
                'name'              => $feed->user->name,
                'email'             => $feed->user->email,
                'campusId'          => $feed->user->user_meta->campus->id,
                'campusName'        => $feed->user->user_meta->campus->name,
                'profile_picture'   => $creatorProfilePicture
            ], 
            'attachment_link'   => $feedAttachment,
                'channel'       => [
                    'channelId'    => isset($feed->channel) ? (int) $feed->channel->id : 0,
                    'channelName'  => isset($feed->channel) ? $feed->channel->name : '',
                        'channelCreator'  => [
                            'name'      => isset($feed->channel) ? $feed->channel->user->name : '',
                            'emailId'   => isset($feed->channel) ? $feed->channel->user->email : ''
                        ]
                ],
                'groupDetails' => [
                    'groupId'           => isset($feed->group) ? (int) $feed->group->id : '',
                    'groupName'         => isset($feed->group) ? $feed->group->name : '',
                    'groupDescription'  => isset($feed->group) ? $feed->group->description : '',
                    'groupImage'        => $groupImage,
                    'isPrivate'         => isset($feed->group) ? $feed->group->is_private : '',
                    'isDiscovery'       => isset($feed->group) ? $feed->group->group_type : '',
                ]
        ];

        if($feed->feed_interests && count($feed->feed_interests))
        {
            foreach($feed->feed_interests as $interest)   
            {
                if(isset($interest) && $interest->image && file_exists(base_path() . '/public/interests/'.$interest->image))
                {
                    $image = url('/interests/'.$interest->image);
                }
                else
                {
                    $image = url('/interests/default.png');    
                }

                $response['interests'][] = [
                    'interestId'        => (int) $interest->id,
                    'name'              => $interest->name,
                    'image'             => $image
                ];
            }
        }

        return $response;
    }

    public function feedTransformCollection($user = null, $feeds = null)
    {
        $result = [];

        if($feeds)
        {
            if($user)
            {
                $userId = $user->id;
            }
            else
            {
                $userId = access()->user()->id;
            }
            
            $reportedFeeds = FeedReport::where(['user_id' => $userId])->pluck('feed_id')->toArray();

            $sr = 0;
            foreach($feeds as $feed)
            {
                if( !$feed->group)
                    continue;
                
                if(!isset($feed->channel))
                {
                    continue;
                }

                if(!isset($feed->channel->user))
                {
                    continue;
                }

                if(! isset($feed->user->user_meta))
                {
                    continue;
                }

                $groupImage =  url('/groups/'.$feed->group->image);
                
                $feedAttachment = '';

                if(isset($feed->is_attachment) && $feed->is_attachment == 1 && file_exists(base_path() . '/public/feeds/'.$feed->user_id.'/'.$feed->attachment))
                {
                    $feedAttachment = url('/feeds/'.$feed->user_id.'/'.$feed->attachment);
                }
                $creatorProfilePicture  =  url('/profile-pictures/'.$feed->user->user_meta->profile_picture);   
                $result[$sr] = [
                    'feedId'            => $feed->id,
                    'description'       => $feed->description,
                    'is_attachment'     => $feed->is_attachment,
                    'attachmentName'    => isset($feed->attachment) ? $feed->attachment : '',
                    'createdAt'         => date('m-d-Y H:i:s', strtotime($feed->created_at)),
                    'createdDateTime'   => date('m-d-Y', strtotime($feed->created_at)),
                    'createdMonthDate'  => date('M d', strtotime($feed->created_at)),
                    'is_reported'       => $feed->is_reported,
                    'reportedByMe'      => in_array($feed->id, $reportedFeeds) ? 1 : 0,
                    'feedCreator'       => [
                        'userId'            => (int) $feed->user->id,
                        'name'              => $feed->user->name,
                        'email'             => $feed->user->email,
                        'campusId'          => $feed->user->user_meta->campus->id,
                        'campusName'        => $feed->user->user_meta->campus->name,
                        'profile_picture'   => $creatorProfilePicture
                    ],
                    'attachment_link'   => $feedAttachment,
                        'interests' => [],
                        'channel'       => [
                            'channelId'    => (int) $feed->channel->id,
                            'channelName'  => $feed->channel->name,
                                'channelCreator'  => [
                                    'name'      => $feed->channel->user->name,
                                    'emailId'   => $feed->channel->user->email
                                ]
                        ],
                        'groupDetails' => [
                            'groupId'           => (int) $feed->group->id,
                            'groupName'         => $feed->group->name,
                            'groupDescription'  => $feed->group->description,
                            'groupImage'        => $groupImage,
                            'isPrivate'         => $feed->group->is_private,
                            'isDiscovery'       => $feed->group->group_type,
                        ]
                ];

                if($feed->feed_interests && count($feed->feed_interests))
                {
                    foreach($feed->feed_interests as $interest)   
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
                $sr++;
            }

        }

        return $result;
    }

    /**
     * Home Feed TransformCollection
     * 
     * @param collection $feeds
     * @return array
     */
    public function homeFeedTransformCollection($feeds = null)
    {
        $result = [];

        if($feeds)
        {
            $sr = 0;
            foreach($feeds as $feed)
            {
                if(! isset($feed->user->user_meta))
                {
                    continue;
                }

                $groupImage = '';
                
                $feedAttachment = '';

                if(isset($feed->is_attachment) && $feed->is_attachment == 1 && file_exists(base_path() . '/public/feeds/'.$feed->user_id.'/'.$feed->attachment))
                {
                    $feedAttachment = url('/feeds/'.$feed->user_id.'/'.$feed->attachment);
                }
                $creatorProfilePicture  =  url('/profile-pictures/'.$feed->user->user_meta->profile_picture);   

                $firstName = explode(' ', $feed->user->name);
                
                $result[$sr] = [
                    'feedId'            => $feed->id,
                    'description'       => $feed->description,
                    'is_attachment'     => $feed->is_attachment,
                    'createdAt'         => date('m-d-Y H:i:s', strtotime($feed->created_at)),
                    'createdDateTime'   => date('m-d-Y', strtotime($feed->created_at)),
                    'feedCreator'       => [
                        'userId'            => (int) $feed->user->id,
                        'name'              => $firstName[0],
                        'email'             => $feed->user->email,
                        'campusId'          => $feed->user->user_meta->campus->id,
                        'campusName'        => $feed->user->user_meta->campus->name,
                        'profile_picture'   => $creatorProfilePicture
                    ],
                    'attachment_link'   => $feedAttachment,
                        'interests' => [],
                        'channel'       => [
                            'channelId'    => 0,
                            'channelName'  => '',
                                'channelCreator'  => [
                                    'name'      => '',
                                    'emailId'   => ''
                                ]
                        ],
                        'groupDetails' => [
                            'groupId'           => isset($feed->group->id) ? (int) $feed->group->id : 0,
                            'groupName'         => isset($feed->group->id)  ? $feed->group->name : '',
                            'groupDescription'  => isset($feed->group->id) ? $feed->group->description : '',
                            'groupImage'        => isset($feed->group->id)  ? $groupImage : '',
                            'isPrivate'         => isset($feed->group->id)  ? $feed->group->is_private : '',
                            'isDiscovery'       => isset($feed->group->id)  ? $feed->group->group_type : ''
                        ]
                ];

                if($feed->feed_interests && count($feed->feed_interests))
                {
                    foreach($feed->feed_interests as $interest)   
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
                $sr++;
            }

        }

        return $result;
    }

    /**
     * Feed TransformCollection Timeline
     * 
     * @param collection $feeds
     * @return array
     */
    public function feedTransformCollectionTimeline($feeds = null)
    {
        $temp       = [];
        $dateArray  = [];

        if($feeds)
        {
            $sr = 0;
            foreach($feeds as $feed)
            {
                $key = $feed['createdDateTime'];

                if(in_array($key, $dateArray))
                {
                    continue;
                }

                $dateArray[] = $key;

                $temp[$sr]['dataKey']       = $key;
                $temp[$sr]['dataMonthKey']  = $feed['createdMonthDate'];

                foreach($feeds as $singleFeed)
                {
                    if($singleFeed['createdDateTime'] == $key)
                    {
                        $temp[$sr]['values'][] = $singleFeed;
                    }
                }

                $sr++;
            }
        }

        return $temp;
    }
}
