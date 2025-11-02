<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ConferenceCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map->only(
            'id','public_id', 'name', 'initial', 'date', 'cover_poster_path', 'city', 'country', 'year', 'online_fee', 'online_fee_usd', 'onsite_fee', 'onsite_fee_usd', 'participant_fee', 'participant_fee_usd', 'deleted_at'
        );
    }
}
