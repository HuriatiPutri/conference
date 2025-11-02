<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AudienceStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'conference_id' => ['nullable', 'exists:conferences,id'],
            'first_name' => ['nullable', 'max:100'],
            'last_name' => ['nullable', 'max:100'],
            'paper_title' => ['nullable', 'max:255'],
            'institution' => ['nullable', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone_number' => ['nullable', 'max:20'],
            'country' => ['nullable', 'max:2'],
            'presentation_type' => ['nullable', 'in:oral,poster,none'],
            'paid_fee' => ['nullable', 'numeric', 'min:0'],
            'payment_status' => ['nullable', 'in:pending_payment,paid,cancelled,refunded'],
            'payment_method' => ['nullable', 'in:transfer_bank,payment_gateway'],
            'payment_proof_path' => ['nullable', 'max:255'],
            'full_paper_path' => ['nullable', 'max:255'],
        ];
    }
}
