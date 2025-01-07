<?php

namespace App\Actions;

use App\Models\User;
use App\Services\ShopifyGraphQLService;
use App\Services\ShopService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class InstallShop
{
    /**
     * The ShopService
     *
     * @var ShopService
     */
    protected $shopService;

    /**
     * The GraphQL client
     *
     * @var ShopifyGraphQLService
     */
    protected $graphQlClient;

    /**
     * The Request
     *
     * @var Request
     */
    protected $request;

    public function __invoke(Request $request, ShopService $shopService, ShopifyGraphQLService $graphQlClient)
    {
        $this->shopService   = $shopService;
        $this->request       = $request;
        $this->graphQlClient = $graphQlClient;

        if ($this->request->missing('code')) {
            return false;
        }

        $shop = $this->createOrRestore();

        $accessToken = $this->getAccessToken();
        if ($accessToken === false) {
            return false;
        }

        // Setup the GraphQl Client
        $this->graphQlClient->setAccessToken($accessToken);
        $this->graphQlClient->setSession();

        // Get shop details
        $shopDetails = $this->getShopDetails();
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
                'owner_name'                           => $shopDetails['shopOwnerName'],
                'plan_display_name'                    => $shopDetails['plan']['displayName'],
                'is_partner_development'               => $shopDetails['plan']['partnerDevelopment'],
                'is_shopify_plus'                      => $shopDetails['plan']['shopifyPlus'],
                'currency'                             => $shopDetails['currencyCode'],
                'timezone'                             => $shopDetails['timezoneAbbreviation'],
                'iana_timezone'                        => $shopDetails['ianaTimezone'],
                'money_format'                         => $shopDetails['currencyFormats']['moneyFormat'],
                'money_with_currency_format'           => $shopDetails['currencyFormats']['moneyWithCurrencyFormat'],
                'money_in_emails_format'               => $shopDetails['currencyFormats']['moneyInEmailsFormat'],
                'money_with_currency_in_emails_format' => $shopDetails['currencyFormats']['moneyWithCurrencyInEmailsFormat'],
                'checkout_api_supported'               => $shopDetails['checkoutApiSupported'],
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
     * @return mixed
     *
     * @throws ConnectionException
     */
    public function getShopDetails()
    {
        $query = <<<'QUERY'
            query {
                shop {
                    name
                    email
                    shopOwnerName
                    currencyCode
                    timezoneAbbreviation
                    ianaTimezone
                    primaryDomain {
                        host
                    }
                    plan {
                        displayName
                        partnerDevelopment
                        shopifyPlus
                    }
                    currencyFormats {
                        moneyFormat
                        moneyInEmailsFormat
                        moneyWithCurrencyFormat
                        moneyWithCurrencyInEmailsFormat
                    }
                    checkoutApiSupported
                }
            }
        QUERY;

        $response = $this->graphQlClient->query($query);

        if ($response['errors'] === false && $response['status'] === 200) {
            return $response['body']->container['data']['shop'];
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
