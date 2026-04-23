<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class GetReportRequest extends FormRequest
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
            // Daily report
            'date' => 'nullable|date|date_format:Y-m-d',

            // Weekly report
            'start_date' => 'nullable|date|date_format:Y-m-d',
            'end_date' => 'nullable|date|date_format:Y-m-d|after_or_equal:start_date',

            // Monthly report
            'year' => 'nullable|integer|min:1900|max:2100',
            'month' => 'nullable|integer|min:1|max:12',

            // Common pagination
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }

    /**
     * Get custom messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'date.date_format' => 'Date must be in format: Y-m-d',
            'start_date.date_format' => 'Start date must be in format: Y-m-d',
            'end_date.date_format' => 'End date must be in format: Y-m-d',
            'end_date.after_or_equal' => 'End date must be after or equal to start date',
            'year.min' => 'Year must be 1900 or later',
            'year.max' => 'Year cannot exceed 2100',
            'month.min' => 'Month must be between 1 and 12',
            'month.max' => 'Month must be between 1 and 12',
            'per_page.max' => 'Per page cannot exceed 100 items',
        ];
    }
}
