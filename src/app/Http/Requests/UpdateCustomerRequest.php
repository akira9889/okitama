<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['string', 'max:15', 'nullable'],
            'last_name' => ['string', 'max:15', 'nullable'],
            'first_kana' => ['string', 'max:15', 'nullable'],
            'last_kana' => ['string', 'max:15', 'nullable'],
            'company' => ['string', 'max:30', 'nullable'],
            'town_id' => ['integer', 'exists:delivery_areas,town_id'],
            'address_number' => ['required', 'string', 'max:15'],
            'building_name' => ['string', 'max:20', 'nullable'],
            'room_number' => ['string', 'max:10', 'nullable'],
            'dropoff_ids' => ['nullable'],
            'dropoff_ids.*' => ['integer', 'exists:dropoffs,id'],
            'absence' => ['required', 'boolean'],
            'only_amazon' => ['required', 'boolean'],
            'description' => ['string', 'nullable']
        ];
    }
}
