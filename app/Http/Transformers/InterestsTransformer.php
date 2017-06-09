<?php

namespace App\Http\Transformers;

use App\Http\Transformers;
use Html;

class InterestsTransformer extends Transformer 
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
        
        if(isset($data) && $data->image && file_exists(base_path() . '/public/interests/'.$data->image))
        {
            $image = url('/interests/'.$data->image);
        }
        else
        {
            $image = url('/interests/default.png');    
        }

        return [
            'interestId'        => (int) $data->id,
            'name'              => $data->name,
            'image'             => $image
        ];
    }
}
