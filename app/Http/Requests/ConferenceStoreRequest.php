<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConferenceStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        // Deteksi apakah ini update (PUT/PATCH) atau create (POST)
        $isUpdate = in_array($this->method(), ['PUT', 'PATCH']);

        return [
            'name' => [$isUpdate ? 'sometimes' : 'required', 'string', 'max:255'],
            'initial' => [$isUpdate ? 'sometimes' : 'required', 'string', 'max:225'],
            'cover_poster_path' => ['nullable', 'file', 'mimes:png,jpg,jpeg', 'max:2048'], // max dalam KB
            'date' => [$isUpdate ? 'sometimes' : 'required', 'date'],
            'city' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'year' => [$isUpdate ? 'sometimes' : 'required', 'integer', 'min:2000', 'max:2100'],
            'online_fee' => ['nullable', 'numeric', 'min:0'],
            'online_fee_usd' => ['nullable', 'numeric', 'min:0'],
            'onsite_fee' => ['nullable', 'numeric', 'min:0'],
            'onsite_fee_usd' => ['nullable', 'numeric', 'min:0'],
            'participant_fee' => ['nullable', 'numeric', 'min:0'],
            'participant_fee_usd' => ['nullable', 'numeric', 'min:0'],
            'certificate_template_path' => ['nullable', 'file', 'mimes:png,jpg,jpeg,pdf', 'max:4096'],
            'certificate_template_position' => ['nullable', 'string', 'max:255'],
            'rooms' => ['nullable', 'array'],
            'rooms.*.room_name' => ['required', 'string', 'max:255'],
            'rooms.*.id' => ['nullable', 'integer'],
        ];
    }
}