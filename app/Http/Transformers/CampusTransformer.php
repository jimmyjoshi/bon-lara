<?php

namespace App\Http\Transformers;

use App\Http\Transformers;

class CampusTransformer extends Transformer 
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
            'campusID'          => (int) $data->id,
            'campusName'        => $data->name,
            'campusCode'        => $data->campus_code,
            'validDomain'       => $data->valid_domain
        ];
    }
}
