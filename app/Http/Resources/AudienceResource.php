<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AudienceResource extends JsonResource
{
    public function toArray($request)
    {
        return array_merge($this->only([
            'id','public_id','conference_id','first_name','last_name',
            'paper_title','institution','email','phone_number','country',
            'presentation_type','paid_fee','payment_status','payment_method',
            'payment_proof_path','full_paper_path','created_at','updated_at','deleted_at',
        ]), [
            // hasMany
            'key_notes' => $this->when(
                $this->relationLoaded('key_notes'),
                function () {
                    return $this->key_notes->map(function ($note) {
                        return [
                            'id' => $note->id,
                            'name_of_participant' => $note->name_of_participant,
                            // tambah field lain jika perlu
                        ];
                    })->values();
                },
                [] // default kosong bila belum di-eager load atau tidak ada
            ),
            'parallel_sessions' => $this->when(
                $this->relationLoaded('parallel_sessions'),
                function () {
                    return $this->parallel_sessions->map(function ($session) {
                        return [
                            'id' => $session->id,
                            'name_of_presenter' => $session->name_of_presenter,
                            'room_id' => $session->room_id,
                            'paper_title' => $session->paper_title,
                            // tambah field lain jika perlu
                        ];
                    })->values();
                },
                [] // default kosong bila belum di-eager load atau tidak ada
            ),
            'conference' => $this->whenLoaded('conference', function () {
                return [
                    'id' => $this->conference->id,
                    'name' => $this->conference->name,
                    'year' => $this->conference->year,
                    'certificate_template_path' => $this->conference->certificate_template_path,
                    'certificate_template_position' => $this->conference->certificate_template_position,
                ];
            }),
        ]);
    }
}