<?php

namespace App\Http\Requests;

use Propaganistas\LaravelPhone\Rules\Phone as PhoneRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Classes\Phone;

class StoreCheckin extends FormRequest
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
    public function rules(): array
    {
        return [
            'mobile' => ['nullable', (new PhoneRule)->mobile()->country('PH')]
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
