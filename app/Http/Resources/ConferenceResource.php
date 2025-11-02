<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ConferenceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'public_id' => $this->public_id,
            'name' => $this->name,
            'initial' => $this->initial,
            'cover_poster_path' => $this->cover_poster_path,
            'date' => $this->date,
            'city' => $this->city,
            'country' => $this->country,
            'year' => $this->year,
            'online_fee' => $this->online_fee,
            'online_fee_usd' => $this->online_fee_usd,
            'onsite_fee' => $this->onsite_fee,
            'onsite_fee_usd' => $this->onsite_fee_usd,
            'participant_fee' => $this->participant_fee,
            'participant_fee_usd' => $this->participant_fee_usd,
            'certificate_template_path' => $this->certificate_template_path,
            'certificate_template_position' => $this->certificate_template_position,
            'deleted_at' => $this->deleted_at,
            'rooms' => $this->rooms,
        ];
    }
}
