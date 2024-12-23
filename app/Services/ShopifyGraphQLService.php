<?php

namespace Barn2App\Services;

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
        $options->setVersion('2024-10');

        // Create the client and session
        $this->client = new BasicShopifyAPI($options);
        $this->client->setSession(new Session($this->shopDomain, $this->accessToken));
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
