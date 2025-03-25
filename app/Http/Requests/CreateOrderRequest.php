<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
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
            'items' => ['required', 'array', 'min:1'],
            'items.*.name' => ['required', 'string'],
            'items.*.price' => ['required', 'numeric', 'min:0.01'],
            'items.*.quantity' => ['required', 'integer', 'min:1']
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'At least one order item is required.',
            'items.min' => 'At least one order item is required.'
        ];
    }
}
