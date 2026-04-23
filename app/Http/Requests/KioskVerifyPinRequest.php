<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KioskVerifyPinRequest extends FormRequest
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
            'pin' => 'required|string|min:1|max:20',
        ];
    }

    /**
     * Get custom messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'pin.required' => 'PIN is required',
            'pin.min' => 'PIN must be at least 1 character',
            'pin.max' => 'PIN cannot exceed 20 characters',
        ];
    }
}
