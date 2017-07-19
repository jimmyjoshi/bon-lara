<?php

namespace App\Http\Transformers;

use App\Http\Transformers;

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
        $groupImage =  url('/groups/'.$feed->group->image);
        
        $feedAttachment = '';

        if(isset($feed->is_attachment) && $feed->is_attachment == 1 && file_exists(base_path() . '/public/feeds/'.$feed->user_id.'/'.$feed->attachment))
        {
            $feedAttachment = url('/feeds/'.$feed->user_id.'/'.$feed->attachment);
        }

        $response = [
            'feedId'            => $feed->id,
            'description'       => $feed->description,
            'is_attachment'     => $feed->is_attachment,
            'attachment_link'   => $feedAttachment,
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

                $response['interests'][] = [
                    'interestId'        => (int) $interest->id,
                    'name'              => $interest->name,
                    'image'             => $image
                ];
            }
        }

        return $response;
    }

    public function feedTransformCollection($feeds = null)
    {
        $result = [];

        if($feeds)
        {
            $sr = 0;
            foreach($feeds as $feed)
            {
                if( !$feed->group)
                    continue;
                
                $groupImage =  url('/groups/'.$feed->group->image);
                
                $feedAttachment = '';

                if(isset($feed->is_attachment) && $feed->is_attachment == 1 && file_exists(base_path() . '/public/feeds/'.$feed->user_id.'/'.$feed->attachment))
                {
                    $feedAttachment = url('/feeds/'.$feed->user_id.'/'.$feed->attachment);
                }
                $result[$sr] = [
                    'feedId'            => $feed->id,
                    'description'       => $feed->description,
                    'is_attachment'     => $feed->is_attachment,
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
}
