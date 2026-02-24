<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'offer_id' => ['required', 'integer', 'exists:job_offers,id'],
            'candidate_user_id' => ['required', 'integer', 'exists:users,id'],
            'status' => ['sometimes', 'in:SENT,IN_REVIEW,ACCEPTED,REJECTED,CANCELED'],
            'company_notes' => ['nullable', 'string'],
        ];
    }
}
