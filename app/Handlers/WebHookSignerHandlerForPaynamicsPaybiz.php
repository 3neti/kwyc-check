<?php

namespace App\Handlers;

use Exception;
use Illuminate\Http\Request;
use Spatie\WebhookClient\WebhookConfig;
use Spatie\WebhookClient\SignatureValidator\SignatureValidator;

class WebHookSignerHandlerForPaynamicsPaybiz implements SignatureValidator
{
    /**
     * Verify request signature
     *
     * @param Request $request
     * @param WebhookConfig $config
     * @return bool
     * @throws Exception
     */
    public function isValid(Request $request, WebhookConfig $config): bool
    {
        $signature = $request->header($config->signatureHeaderName);

        /**
         * If the sender don't add signature and you trust it- you can return true -- I won't recommend this
         */
        if (! $signature) {
            return true;
        }

        $signingSecret = $config->signingSecret;

        if (empty($signingSecret)) {
            throw new Exception("No secret key");
        }

        //Name of hashing algorithm used by the sender- "sha256" others includes - "haval160,4" "md5", etc..
        $computedSignature = hash_hmac('sha256', $request->getContent(), $signingSecret);

        return hash_equals($signature, $computedSignature);
    }
}
