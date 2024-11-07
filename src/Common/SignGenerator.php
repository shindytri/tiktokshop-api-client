<?php

namespace Aftwork\TiktokShop\Common;

class SignGenerator
{
    public static function generateSign($apiPathName, $appSecret, &$params, $body = null)
    {
        $paramsToBeSigned = $params;
        $stringToBeSigned = '';

        // 1. Extract all query param EXCEPT ' sign ', ' access_token ', reorder the params based on alphabetical order.
        unset($paramsToBeSigned['sign'], $paramsToBeSigned['access_token']);
        ksort($paramsToBeSigned);

        // 2. Concat all the param in the format of {key}{value}
        foreach ($paramsToBeSigned as $k => $v) {
            if (!is_array($v)) {
                $stringToBeSigned .= "$k$v";
            }
        }

        // 3. Append the request path to the beginning
        $stringToBeSigned = $apiPathName . $stringToBeSigned;

        // 4. Push request body if multipart/form-data, push request body to string
        if (!is_null($body) && !empty($body)) $stringToBeSigned = $stringToBeSigned . json_encode($body);

        // 5. Wrap string generated in step 3 with app_secret.
        $stringToBeSigned = $appSecret . $stringToBeSigned . $appSecret;

        // 6. Use sha256 to generate sign with salt(secret).
        return hash_hmac('sha256', $stringToBeSigned, $appSecret);
    }
}