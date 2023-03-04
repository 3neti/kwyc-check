<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Laravel\Fortify\Rules\Password;
use JetBrains\PhpStorm\ArrayShape;
use Laravel\Jetstream\Jetstream;
use App\Classes\Phone;

class StoreRecruitedAgentRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    #[ArrayShape(['name' => "string[]", 'email' => "string[]", 'mobile' => "string[]", 'password' => "array", 'password_confirmation' => "array", 'terms' => "string|string[]"])] public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'mobile' => ['required', 'string', 'max:18', 'unique:users'],
            'password' => ['required', 'string', new Password, 'confirmed'],
            'password_confirmation' => ['required', 'string', new Password],
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ];
    }

    /**
     * @return array
     */
    public function validationData(): array
    {
        $data = parent::validationData();

        return array_merge($data, [
            'mobile' => Phone::number($data['mobile']),
        ]);
    }
}
