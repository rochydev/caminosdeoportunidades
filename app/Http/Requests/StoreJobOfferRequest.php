<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobOfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_user_id' => ['required', 'integer', 'exists:users,id'],
            'category_id' => ['nullable', 'integer', 'exists:job_categories,id'],
            'contract_type_id' => ['nullable', 'integer', 'exists:contract_types,id'],
            'workday_type_id' => ['nullable', 'integer', 'exists:workday_types,id'],
            'modality_id' => ['nullable', 'integer', 'exists:modality_types,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'requirements' => ['nullable', 'string'],
            'adaptations' => ['nullable', 'string'],
            'city' => ['nullable', 'string', 'max:120'],
            'is_adapted' => ['sometimes', 'boolean'],
            'status' => ['sometimes', 'in:DRAFT,PUBLISHED,CLOSED'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:tags,id'],
            'disabilities' => ['nullable', 'array'],
            'disabilities.*' => ['exists:disability_types,id'],
        ];
    }
}
