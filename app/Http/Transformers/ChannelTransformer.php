<?php

namespace App\Http\Transformers;

use App\Http\Transformers;

class ChannelTransformer extends Transformer 
{
    /**
     * Transform
     * 
     * @param array $data
     * @return array
     */
    public function transform($channel) 
    {
        if(is_array($channel))
        {
            $data = (object)$channel;
        }
        
        return [
            'channelId'    => (int) $channel->id,
            'channelName'  => $channel->name,
            'campusId'     => $channel->campus_id,
            'groupId'      => $channel->group_id,
            'channelCreator'  => [
                'name'      => $channel->user->name,
                'emailId'   => $channel->user->email,
            ]
        ];
    }

    /**
     * Create Channel
     * 
     * @param object $model
     * @return array
     */
    public function createChannel($model = null)
    {
        $result = []; 

        if($model)
        {
            return [
                'channelId'    => (int) $model->id,
                'channelName'  => $model->name,
                'campusId'     => $model->campus_id,
                'groupId'      => $model->group_id,
                'channelCreator'  => [
                    'userId'    => (int) $model->user->id,
                    'name'      => $model->user->name,
                    'emailId'   => $model->user->email,
                ]
            ];
        }

        return $result;
    }

    /**
     * Transform Channel Collection
     * 
     * @param array $data
     * @return array
     */
    public function transformChannelCollection($channels = null) 
    {
        $result = []; 

        if($channels)
        {
            foreach($channels as $channel)
            {
                $result[] = [
                    'channelId'    => (int) $channel->id,
                    'channelName'  => $channel->name,
                    'campusId'     => $channel->campus_id,
                    'groupId'      => $channel->group_id,
                    'channelCreator'  => [
                        'userId'    => (int) $channel->user->id,
                        'name'      => $channel->user->name,
                        'emailId'   => $channel->user->email,
                    ]
                ];
            }
        }

        return $result;
    }
}
