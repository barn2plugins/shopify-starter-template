<?php

namespace Barn2App\Services;

use Barn2App\Models\User;

class ShopifyWebhookService
{
    /**
     * The User model.
     *
     * @var \Barn2App\Models\User
     */
    private $shop;

    /**
     * The Shopify REST API service.
     *
     * @var \Barn2App\Services\ShopifyRestService
     */
    private $restAPI;

    public function __construct(ShopifyRestService $restAPI)
    {
        $this->restAPI = $restAPI;
    }

    /**
     * Register a webhook for the shop in Shopify.
     *
     * @param  \Barn2App\Models\User  $shop  The shop to register the webhook for.
     * @return bool
     */
    public function register(User $shop)
    {
        $this->shop = $shop;

        if (! $shop) {
            return false;
        }

        $this->registerAppUninstalledWebhook();
        $this->registerCustomerDataRequestWebhook();
        $this->registerCustomerRedactWebhook();
        $this->registerShopRedactWebhook();

    }

    /**
     * Register the webhook for the app/uninstalled event.
     *
     * @return mixed
     */
    public function registerAppUninstalledWebhook()
    {
        // Register the webhook for the app/uninstalled event
        $url = '/webhooks.json';
        $data = [
            'webhook' => [
                'topic' => 'app/uninstalled',
                'address' => route('webhook.app.uninstalled'),
                'format' => 'json',
            ],
        ];

        return $this->restAPI->post($url, $data);
    }

    /**
     * Register the webhook for the customers/data_request event.
     *
     * @return mixed
     */
    public function registerCustomerDataRequestWebhook()
    {
        // Register the webhook for the app/uninstalled event
        $url = '/webhooks.json';
        $data = [
            'webhook' => [
                'topic' => 'customers/data_request',
                'address' => route('webhook.customers.data_request'),
                'format' => 'json',
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
        $url = '/webhooks.json';
        $data = [
            'webhook' => [
                'topic' => 'customers/redact',
                'address' => route('webhook.customers.redact'),
                'format' => 'json',
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
        $url = '/webhooks.json';
        $data = [
            'webhook' => [
                'topic' => 'shop/redact',
                'address' => route('webhook.shop.redact'),
                'format' => 'json',
            ],
        ];

        return $this->restAPI->post($url, $data);
    }
}
