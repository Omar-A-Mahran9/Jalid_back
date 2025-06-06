<?php

namespace App\Http\Requests\Api;

use App\Rules\PhoneNumber;
use Illuminate\Foundation\Http\FormRequest;

class StoreContact_usRequest extends FormRequest
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
            'name' => ['required'],
            'phone' => ['required', new PhoneNumber()],
            'email' => ['nullable', 'string', 'email:rfc,dns'],
             'addon_service_id' => ['required_if:main_contactUs,true', 'numeric'],

            'message' => ['required']
        ];
    }
}
