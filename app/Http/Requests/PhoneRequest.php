<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class PhoneRequest extends FormRequest
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
            "phone" => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) {
                    $hashed_phone = Hash("SHA256" , $value);
                    if (!User::where('phone_hashed', $hashed_phone)->exists()) {
                        $fail('Account Not Found');
                    }
                },
            ],
        ];
    }
}
