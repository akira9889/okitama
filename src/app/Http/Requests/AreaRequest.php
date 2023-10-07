<?php

namespace App\Http\Requests;

use App\Models\Town;
use Illuminate\Foundation\Http\FormRequest;

class AreaRequest extends FormRequest
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
        $cityId = $this->input('city_id');

        return [
            'prefecture_id' => ['required', 'integer', 'exists:prefectures,id'],
            'city_id' => ['required', 'max:5', 'regex:/\A^[0-9]+\z/'],
            'city_name' => ['required', 'string', 'max:20'],
            'town_name' => ['required', 'string', 'max:20', function ($attribute, $value, $fail) use ($cityId) {
                $exists = Town::where('city_id', $cityId)
                            ->where('name', $value)
                            ->exists();
                if ($exists) {
                    $fail('このエリアは登録されております。');
                }
            }],
        ];
    }
}
