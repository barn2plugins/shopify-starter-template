<?php

namespace App\Http\Controllers;

use App\Actions\Hmac;
use App\Exceptions\SignatureVerificationException;
use App\Services\ShopService;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    /**
     * The shop service.
     *
     * @var \App\Services\ShopService
     */
    protected $shopService;

    /**
     * The request object.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Class constructor to initialize dependencies.
     *
     * This constructor initializes the shop service to handle shop-related logic.
     *
     * @param  \App\Services\ShopService  $shopService  The service responsible for handling shop-related logic.
     */
    public function __construct(Request $request, ShopService $shopService)
    {
        $this->request     = $request;
        $this->shopService = $shopService;
    }

    /**
     * Handle an app uninstallation webhook from Shopify
     */
    public function uninstalled()
    {
        if (Hmac::verify($this->request) === false) {
            throw new SignatureVerificationException('Unable to verify signature.');
        }

        if (! $this->request->has('myshopify_domain')) {
            return;
        }

        $shopDomain = $this->request->input('myshopify_domain');
        $shop       = $this->shopService->getShop($shopDomain);

        if (! $shop->exists()) {
            return;
        }

        $shop->shopService->deleteShop($shop);
    }

    /**
     * Handle a customer data request webhook from Shopify
     *
     * @return void
     */
    public function customersDataRequest()
    {
        if (Hmac::verify($this->request) === false) {
            throw new SignatureVerificationException('Unable to verify signature.');
        }

        if (! $this->request->has('shop_domain')) {
            return;
        }

        $shopDomain = $this->request->input('shop_domain');
        $shop       = $this->shopService->getShop($shopDomain);

        // Do the necessary logic here
    }

    /**
     * Handle a customer data redaction webhook from Shopify
     *
     * @return void
     */
    public function customersRedact()
    {
        if (Hmac::verify($this->request) === false) {
            throw new SignatureVerificationException('Unable to verify signature.');
        }

        if (! $this->request->has('shop_domain')) {
            return;
        }

        $shopDomain = $this->request->input('shop_domain');
        $shop       = $this->shopService->getShop($shopDomain);

        // Do the necessary logic here
    }

    /**
     * Handle a customer data redaction webhook from Shopify
     *
     * @return void
     */
    public function shopRedact()
    {
        if (Hmac::verify($this->request) === false) {
            throw new SignatureVerificationException('Unable to verify signature.');
        }

        if (! $this->request->has('shop_domain')) {
            return;
        }

        $shopDomain = $this->request->input('shop_domain');
        $shop       = $this->shopService->getShop($shopDomain);

        // Do the necessary logic here
    }

    /**
     * Handle a customer data shop update webhook from Shopify
     *
     * @return void
     */
    public function shopUpdate()
    {
        if (Hmac::verify($this->request) === false) {
            throw new SignatureVerificationException('Unable to verify signature.');
        }

        if (! $this->request->has('shop_domain')) {
            return;
        }

        // Typically we will track if the shop went live
        $shopDomain = $this->request->input('shop_domain');
        $planName   = $this->request->input('plan_name');
        if ($planName === 'partner_test' || $planName === 'development' || $planName === 'affiliate') {
            // Ignore these plans
            return;
        }

        $shop = $this->shopService->getShop($shopDomain);
        $shop->update([
            'is_partner_development' => false,
        ]);

        // Do some more necessary actions

    }
}
