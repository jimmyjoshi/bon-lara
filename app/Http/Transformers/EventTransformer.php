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
        
        return [
            'eventId'           => (int) $data->id,
            'eventName'         => $data->name,
            'eventStartDate'    => $this->nulltoBlank($data->start_date),
            'eventEndDate'      => $this->nulltoBlank($data->end_date)
        ];
    }
}
