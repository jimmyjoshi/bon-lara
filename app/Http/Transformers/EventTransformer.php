<?php

namespace App\Http\Transformers;

use App\Http\Transformers;

class EventTransformer extends Transformer 
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
            'eventStartDate'    => date('m-d-Y H:i:s', strtotime($data->start_date)),
            'eventEndDate'      => date('m-d-Y H:i:s', strtotime($data->end_date)),
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
        $creatorProfilePicture =  url('/profile-pictures/'.$model->user->user_meta->profile_picture);

        return [
            'eventId'           => (int) $model->id,
            'eventName'         => $model->name,
            'eventTitle'        => $model->title,
            'eventStartDate'    => date('m-d-Y H:i:s', strtotime($model->start_date)),
            'eventEndDate'      => date('m-d-Y H:i:s', strtotime($model->end_date)),
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

    public function getAllGroupEvents($events = null, $userInfo = null)
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
                    'eventStartDate'    => date('d M', strtotime($event->start_date)),
                    'eventEndDate'      => date('d M', strtotime($event->end_date)),
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
}
