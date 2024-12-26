<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            "name" => 'required|string|max:255',
            "phone" => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) {
                    $hashed_phone = Hash("SHA256", $value);
                    if (User::where('phone_hashed', $hashed_phone)->exists()) {
                        $fail('The provided Phone is taken');
                    }
                },
            ],
            "device_id" => 'required|string',
            "device_type" => 'required|string',
            "fcm_token" => 'required|string',
            "profile_img" => [
                'required',
                'file',
                'image',
                'mimes:jpeg,png,jpg,gif',
                'max:2048'
            ],
            "email" => [
                'required',
                'email',
                function ($attribute, $value, $fail) {
                    $hashed_email = Hash("SHA256", $value);
                    if (User::where('email_hashed', $hashed_email)->exists()) {
                        $fail('The provided Email is taken');
                    }
                },
            ],
        ];
    }
}
