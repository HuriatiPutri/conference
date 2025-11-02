<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class AudienceCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => AudienceResource::collection($this->collection),
        ];
    }
}
