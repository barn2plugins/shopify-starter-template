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
     * Make a GET request to the Shopify REST API
     *
     * @param  mixed  $url
     * @return RedirectResponse|JsonResponse
     */
    public function get($url)
    {
        try {
            $response = Http::withHeaders([
                'X-Shopify-Access-Token' => $this->accessToken,
            ])->get($this->rest_url.$url);

            if ($response->failed()) {
                return false;
            }

            return $response->json();
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Make a POST request to the Shopify REST API
     *
     * @param  mixed  $url
     * @return RedirectResponse|JsonResponse
     */
    public function post($url, $data)
    {
        try {
            $response = Http::withHeaders([
                'X-Shopify-Access-Token' => $this->accessToken,
            ])->post($this->rest_url.$url, $data);

            if ($response->failed()) {
                return false;
            }

            return $response->json();
        } catch (\Exception $e) {
            return false;
        }
    }
}
