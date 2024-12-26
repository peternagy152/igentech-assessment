<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'profile_img' => [
                'required',
                'file',
                'image',
                'mimes:jpeg,png,jpg,gif',
                'max:2048'
            ],
            'email' => [
                'required',
                'email',
                function ($attribute, $value, $fail) {

                    $current_hashed_email = $this->user()->email_hashed ; 
                    $pass_hashed_email =  hash('sha256', $value) ; 
                    if ($current_hashed_email !== $pass_hashed_email) {
                        if (User::where('email_hashed', $pass_hashed_email)->exists()) {
                            $fail('The provided Email is already taken.');
                        }
                    }
                },
            ],
        ];
    }
    
}
