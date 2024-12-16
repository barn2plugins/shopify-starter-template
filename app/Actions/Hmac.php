<?php

namespace Barn2App\Actions;

use Illuminate\Http\Request;

/**
 * Class Hmac
 *
 * This class provides methods for handling HMAC (Hash-based Message Authentication Code)
 * verification for requests, ensuring data integrity and authenticity. It is typically
 * used for verifying requests from external services like Shopify.
 */
class Hmac
{
    /**
     * Verifies the HMAC of a given request to ensure the integrity of the request parameters.
     *
     * This method calculates the HMAC for the provided request parameters using the
     * secret key and compares it with the HMAC value provided in the request.
     * If the 'hmac' parameter is missing, it returns null.
     *
     * @param  Request  $request  The incoming HTTP request to verify.
     * @return bool|null Returns true if the HMAC is valid, false if invalid, or null if the HMAC is missing.
     */
    public static function verify(Request $request)
    {
        if ($request->missing('hmac')) {
            return null;
        }

        $params = $request->except('hmac');
        $hmac = $request->input('hmac');
        $secret = config('shopify.api_secret');

        // Sort parameters by key and create a query string.
        ksort($params);
        $query = http_build_query($params);

        // Calculate the HMAC using the secret key.
        $calculatedHmac = hash_hmac('sha256', $query, $secret);

        // Compare the calculated HMAC with the one provided in the request.
        return hash_equals($calculatedHmac, $hmac);
    }
}
