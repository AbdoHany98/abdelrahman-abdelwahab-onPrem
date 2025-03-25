<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApproveOrderRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'approved' => ['required', 'boolean'],
            'notes' => ['sometimes', 'nullable', 'string', 'max:500']
        ];
    }
}
