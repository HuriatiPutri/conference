<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConferenceTemplateSettingRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'certificate_template_path' => ['nullable', 'file', 'mimes:png,jpg,jpeg', 'max:4096'],
            'certificate_template_position' => ['nullable', 'json'],
        ];
    }
}