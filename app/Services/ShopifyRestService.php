<?php

namespace Barn2App\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ShopifyRestService
{
    /**
     * The shop domain
     *
     * @var string
     */
    protected $shopDomain;

    /**
     * The access token
     *
     * @var string
     */
    protected $accessToken;

    /**
     * Holds the BasicShopifyAPI client
     *
     * @var BasicShopifyAPI
     */
    protected $client;

    /**
     * The REST URL
     *
     * @var string
     */
    protected $rest_url;

    /**
     * Create a new ShopifyGraphQLService instance
     *
     * @return void
     */
    public function __construct(
        Request $request,
        ShopService $shopService
    ) {
        $this->shopDomain = $shopService->getShopDomain($request);
        $this->accessToken = $shopService->getAccessToken($request);
        $this->rest_url = "https://{$this->shopDomain}/admin/api/2024-10";
    }

    /**
     * Create or update the subscription
     *
     * @param  mixed  $url
     * @return RedirectResponse|JsonResponse
     */
    public function request($url)
    {
        try {
            $response = Http::withHeaders([
                'X-Shopify-Access-Token' => $this->accessToken,
            ])->get($this->rest_url.$url);

            if ($response->failed()) {
                return false;
            }

            $chargeDetails = $response->json();

            if (empty($chargeDetails['recurring_application_charge'])) {
                return false;
            }

            return $chargeDetails;
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
