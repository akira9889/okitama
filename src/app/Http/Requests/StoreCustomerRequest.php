<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
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
            'last_name' => ['required', 'string', 'max:15'],
            'town_id' => ['integer', 'exists:delivery_areas,town_id'],
            'is_checked_default' => ['required', 'boolean'],
            'address_number' => ['string', 'nullable'],
            'room_number' => ['string', 'nullable'],
            'dropoff_ids.*' => ['integer', 'exists:dropoffs,id'],
            'description' => ['string']
        ];
    }
}
