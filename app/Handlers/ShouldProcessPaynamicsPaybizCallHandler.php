<?php

namespace App\Handlers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\WebhookClient\WebhookProfile\WebhookProfile;

class ShouldProcessPaynamicsPaybizCallHandler implements WebhookProfile
{
    public function shouldProcess(Request $request): bool
    {
        return true;

        $validator = Validator::make($request->all(), [
            'merchant_id' => 'required',
            'customer_info.zip' => 'required',
            'customer_info.mobile' => 'required|phone:PH',
            'customer_info.amount' => 'required|numeric|between:10,10000',
            'pay_reference' => 'required',
            'response_code' => 'required'
        ]);

        return  $validator->passes();
    }
}
