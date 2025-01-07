<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;

/**
 * This service registers the webhooks with Shopify during the app installation process
 */
class ShopifyWebhookService
{
    /**
     * The User model.
     *
     * @var \App\Models\User
     */
    private $shop;

    /**
     * The Shopify Shop Service
     *
     * @var \App\Services\ShopService
     */
    private $shopService;

    /**
     * The Shopify Graph QL Service
     *
     * @var \App\Services\ShopifyGraphQLService
     */
    private $graphQlClient;

    /**
     * The Shopify REST API Service
     *
     * @var \App\Services\ShopifyRestService
     */
    private $restAPI;

    public function __construct(ShopService $shopService, ShopifyGraphQLService $graphQlClient, ShopifyRestService $restAPI)
    {
        $this->shopService   = $shopService;
        $this->graphQlClient = $graphQlClient;
        $this->restAPI       = $restAPI;
    }

    /**
     * Register a webhook for the shop in Shopify.
     *
     * @param  \App\Models\User  $shop  The shop to register the webhook for.
     * @return bool
     */
    public function register(Request $request, User $shop)
    {
        $this->shop = $shop;

        if (! $this->shop) {
            return false;
        }

        // Setup graphql client
        $this->setupGraphQlClient($request);

        // register application webhooks with Shopify
        $this->registerAppUninstalledWebhook();
        $this->registerCustomerDataRequestWebhook();
        $this->registerCustomerRedactWebhook();
        $this->registerShopRedactWebhook();
        $this->registerShopUpdateWebhook();
    }

    /**
     * Sets up the GraphQL client with the access token and session for the shop.
     *
     * This method retrieves the access token for the shop using the shop service
     * and applies it to the GraphQL client. It then initializes the session for
     * the client to ensure authenticated requests can be made.
     *
     * @param  \Illuminate\Http\Request  $request  The HTTP request object containing shop information.
     * @return void
     */
    public function setupGraphQlClient(Request $request)
    {
        $accessToken = $this->shopService->getAccessToken($request);
        $this->graphQlClient->setAccessToken($accessToken);
        $this->graphQlClient->setSession();
    }

    /**
     * Register the webhook for the APP_UNINSTALLED event.
     *
     * @return mixed
     */
    public function registerAppUninstalledWebhook()
    {
        // Register the webhook for the APP_UNINSTALLED event
        $query = <<<'QUERY'
            mutation webhookSubscriptionCreate($topic: WebhookSubscriptionTopic!, $webhookSubscription: WebhookSubscriptionInput!) {
                webhookSubscriptionCreate(topic: $topic, webhookSubscription: $webhookSubscription) {
                    userErrors {
                        field
                        message
                    }
                    webhookSubscription {
                        id
                        topic
                        filter
                        format
                        endpoint {
                            __typename
                            ... on WebhookHttpEndpoint {
                                callbackUrl
                            }
                        }
                    }
                }
            }
        QUERY;

        $variables = [
            'topic'               => 'APP_UNINSTALLED',
            'webhookSubscription' => [
                'callbackUrl' => route('webhook.app.uninstalled'),
                'format'      => 'JSON',
                'filter'      => 'type:lookbook',
            ],
        ];

        return $this->graphQlClient->query($query, $variables);
    }

    /**
     * Register the webhook for the customers/data_request event.
     *
     * @return mixed
     */
    public function registerCustomerDataRequestWebhook()
    {
        // Register the webhook for the app/uninstalled event
        $url  = '/webhooks.json';
        $data = [
            'webhook' => [
                'topic'   => 'customers/data_request',
                'address' => route('webhook.customers.data_request'),
                'format'  => 'json',
            ],
        ];

        return $this->restAPI->post($url, $data);
    }

    /**
     * Register the webhook for the customers/redact event.
     *
     * @return mixed
     */
    public function registerCustomerRedactWebhook()
    {
        // Register the webhook for the app/uninstalled event
        $url  = '/webhooks.json';
        $data = [
            'webhook' => [
                'topic'   => 'customers/redact',
                'address' => route('webhook.customers.redact'),
                'format'  => 'json',
            ],
        ];

        return $this->restAPI->post($url, $data);
    }

    /**
     * Register the webhook for the shop/redact event.
     *
     * @return mixed
     */
    public function registerShopRedactWebhook()
    {
        // Register the webhook for the app/uninstalled event
        $url  = '/webhooks.json';
        $data = [
            'webhook' => [
                'topic'   => 'shop/redact',
                'address' => route('webhook.shop.redact'),
                'format'  => 'json',
            ],
        ];

        return $this->restAPI->post($url, $data);
    }

    /**
     * Register the webhook for the shop/redact event.
     *
     * @return mixed
     */
    public function registerShopUpdateWebhook()
    {
        // Register the webhook for the SHOP_UPDATE event
        $query = <<<'QUERY'
            mutation webhookSubscriptionCreate($topic: WebhookSubscriptionTopic!, $webhookSubscription: WebhookSubscriptionInput!) {
                webhookSubscriptionCreate(topic: $topic, webhookSubscription: $webhookSubscription) {
                    userErrors {
                        field
                        message
                    }
                    webhookSubscription {
                        id
                        topic
                        filter
                        format
                        endpoint {
                            __typename
                            ... on WebhookHttpEndpoint {
                                callbackUrl
                            }
                        }
                    }
                }
            }
        QUERY;

        $variables = [
            'topic'               => 'SHOP_UPDATE',
            'webhookSubscription' => [
                'callbackUrl' => route('webhook.shop.update'),
                'format'      => 'JSON',
                'filter'      => 'type:lookbook',
            ],
        ];

        return $this->graphQlClient->query($query, $variables);
    }
}
