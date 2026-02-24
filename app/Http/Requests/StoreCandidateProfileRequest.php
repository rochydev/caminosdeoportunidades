<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCandidateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id', 'unique:candidate_profiles,user_id'],
            'first_name' => ['required', 'string', 'max:80'],
            'last_name' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'city' => ['nullable', 'string', 'max:120'],
            'photo_url' => ['nullable', 'string', 'max:500'],
            'disability_type_id' => ['nullable', 'integer', 'exists:disability_types,id'],
            'disability_degree' => ['nullable', 'integer', 'min:0', 'max:100'],
            'accessibility_needs' => ['nullable', 'string', 'max:255'],
        ];
    }
}
