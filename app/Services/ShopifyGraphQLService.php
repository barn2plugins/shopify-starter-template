<?php

namespace App\Services;

use Gnikyt\BasicShopifyAPI\BasicShopifyAPI;
use Gnikyt\BasicShopifyAPI\Options;
use Gnikyt\BasicShopifyAPI\Session;
use Illuminate\Http\Request;

class ShopifyGraphQLService
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
     * Create a new ShopifyGraphQLService instance
     *
     * @return void
     */
    public function __construct(
        Request $request,
        ShopService $shopService
    ) {
        $this->shopDomain  = $shopService->getShopDomain($request);
        $this->accessToken = $shopService->getAccessToken($request);

        // Create options for the API
        $options = new Options;
        $options->setVersion('2025-01');

        // Create the client and session
        $this->client = new BasicShopifyAPI($options);
        $this->setSession();
    }

    /**
     * Sets the session for the Shopify client.
     *
     * This method initializes a new Shopify session using the shop domain
     * and access token, and assigns it to the client.
     *
     * @return void
     */
    public function setSession()
    {
        $this->client->setSession(new Session($this->shopDomain, $this->accessToken));
    }

    /**
     * Sets the shop domain for the Shopify client.
     *
     * This method assigns the given shop domain to the internal property,
     * which is used to initialize the Shopify session or API requests.
     *
     * @param  string  $shopDomain  The Shopify shop domain (e.g., "example.myshopify.com").
     * @return void
     */
    public function setShopDomain($shopDomain)
    {
        $this->shopDomain = $shopDomain;
    }

    /**
     * Sets the access token for the Shopify client.
     *
     * This method assigns the given access token to the internal property,
     * which is used to authenticate API requests to the Shopify store.
     *
     * @param  string  $accessToken  The access token for the Shopify API.
     * @return void
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * Query the Shopify GraphQL API
     *
     * @param  string  $query  The GraphQL query
     * @param  array  $variables  The query variables
     * @return mixed
     */
    public function query($query, $variables = [])
    {
        return $this->client->graph($query, $variables);
    }
}
