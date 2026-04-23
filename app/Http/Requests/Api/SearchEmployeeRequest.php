<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SearchEmployeeRequest extends FormRequest
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
            'q' => 'nullable|string|min:1|max:100',
            'role' => 'nullable|string|in:admin,employee,hr,super_admin|max:20',
            'department_id' => 'nullable|integer|exists:departments,id',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }

    /**
     * Get custom messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'q.max' => 'Search query cannot exceed 100 characters',
            'role.in' => 'Invalid role specified',
            'department_id.exists' => 'Department not found',
            'per_page.max' => 'Per page cannot exceed 100 items',
        ];
    }
}
