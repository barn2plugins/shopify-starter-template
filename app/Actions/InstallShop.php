<?php

namespace App\Actions;

use App\Models\User;
use App\Services\ShopService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class InstallShop
{
    protected $shopService;

    protected $request;

    public function __invoke(Request $request, ShopService $shopService)
    {
        $this->shopService = $shopService;
        $this->request     = $request;

        if ($this->request->missing('code')) {
            return false;
        }

        $shop = $this->createOrRestore();

        $accessToken = $this->getAccessToken();
        if ($accessToken === false) {
            return false;
        }
        $shopDetails = $this->getShopDetails($accessToken);
        if (! $shopDetails) {
            return $shop;
        }

        // Update the shop
        $shop->update([
            'password'          => $accessToken,
            'email'             => $shopDetails['email'],
            'email_verified_at' => now(),
        ]);

        $shop->store()->create(
            [
                'name'                                 => $shopDetails['name'],
                'owner_name'                           => $shopDetails['shop_owner'],
                'plan'                                 => $shopDetails['plan_name'],
                'plan_display_name'                    => $shopDetails['plan_display_name'],
                'is_partner_development'               => $this->isDevelopmentStore($shopDetails['plan_name']),
                'country_code'                         => $shopDetails['country_code'],
                'currency'                             => $shopDetails['currency'],
                'timezone'                             => $shopDetails['timezone'],
                'iana_timezone'                        => $shopDetails['iana_timezone'],
                'money_format'                         => $shopDetails['money_format'],
                'money_with_currency_format'           => $shopDetails['money_with_currency_format'],
                'money_in_emails_format'               => $shopDetails['money_in_emails_format'],
                'money_with_currency_in_emails_format' => $shopDetails['money_with_currency_in_emails_format'],
                'checkout_api_supported'               => $shopDetails['checkout_api_supported'],
            ]
        );

        return $shop;
    }

    /**
     * Create a new shop or restore a soft-deleted shop.
     *
     * @return \App\Models\User
     */
    public function createOrRestore()
    {
        // Get the shop domain
        $shopDomain = $this->shopService->getShopDomain($this->request);

        // Get the shop
        $shop = User::where('name', $shopDomain)->first();

        if (! $shop) {
            $shop = User::create([
                'name'  => $shopDomain,
                'email' => 'shop@'.$shopDomain,
            ]);
        } elseif ($shop->trashed()) {
            $shop->restore();
        }

        return $shop;
    }

    /**
     * Get the access token from Shopify.
     *
     * @return string|bool
     */
    public function getAccessToken()
    {
        $shopDomain = $this->shopService->getShopDomain($this->request);
        $code       = $this->request->get('code');

        $url = "https://{$shopDomain}/admin/oauth/access_token";

        $params = [
            'client_id'     => config('shopify.api_key'),
            'client_secret' => config('shopify.api_secret'),
            'code'          => $code,
        ];

        $response = Http::asForm()->post($url, $params);

        if ($response->failed()) {
            return false;
        }

        return $response->json('access_token');
    }

    /**
     * Get the Shopify shop details
     *
     * @param  mixed  $accessToken
     * @return mixed
     *
     * @throws ConnectionException
     */
    public function getShopDetails($accessToken)
    {
        $shopDomain = $this->shopService->getShopDomain($this->request);

        $endpoint = "https://$shopDomain/admin/api/2024-10/shop.json";

        $response = Http::withHeaders([
            'X-Shopify-Access-Token' => $accessToken,
        ])->get($endpoint);

        if ($response->successful()) {
            $shopDetails = $response->json();
            return $shopDetails['shop'];
        }

        return false;
    }

    /**
     * Check if the store a development store
     *
     * @param  mixed  $planName
     * @return bool
     */
    public function isDevelopmentStore($planName)
    {
        $isDevelopmentStore = false;
        if ($planName === 'partner_test' || $planName === 'development' || $planName === 'affiliate') {
            $isDevelopmentStore = true;
        }

        return $isDevelopmentStore;
    }
}
