<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class GetAttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'from_date' => 'nullable|date|date_format:Y-m-d',
            'to_date' => 'nullable|date|date_format:Y-m-d|after_or_equal:from_date',
            'user_id' => 'nullable|integer|exists:users,id',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }

    /**
     * Get custom messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'from_date.date_format' => 'From date must be in format: Y-m-d',
            'to_date.date_format' => 'To date must be in format: Y-m-d',
            'to_date.after_or_equal' => 'To date must be after or equal to from date',
            'user_id.exists' => 'User not found',
            'per_page.max' => 'Per page cannot exceed 100 items',
        ];
    }
}
