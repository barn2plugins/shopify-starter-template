<?php

namespace Barn2App\Http\Controllers;

use Barn2App\Services\ShopifyRestService;
use Barn2App\Services\ShopService;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * The request object
     *
     * @var Request
     */
    protected $request;

    /**
     * The shop service
     *
     * @var ShopService
     */
    protected $shopService;

    /**
     * The Shopify REST API service
     *
     * @var ShopifyRestService
     */
    protected $restAPI;

    /**
     * Create a new SubscriptionController instance
     *
     * @return void
     */
    public function __construct(
        Request $request,
        ShopService $shopService,
        ShopifyRestService $restAPI
    ) {
        $this->request = $request;
        $this->shopService = $shopService;
        $this->restAPI = $restAPI;
    }

    /**
     * Handle the subscription charge
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function subscription()
    {
        if (! $this->request->has('charge_id')) {
            return redirect()->route('home');
        }

        $chargeId = $this->request->get('charge_id');

        $url = "/recurring_application_charges/{$chargeId}.json";

        $response = $this->restAPI->request($url);

        if ($response === false) {
            return redirect()->route('home');
        }

        $this->createOrUpdateSubscription($response['recurring_application_charge']);

        return redirect()->route('home');
    }

    /**
     * Create or update the subscription
     *
     * @param  array  $chargeDetails
     * @return mixed
     */
    public function createOrUpdateSubscription($chargeDetails)
    {
        if ($this->subscriptionExists()) {
            $subscription = $this->updateSubscription($chargeDetails);
        } else {
            $subscription = $this->createSubscription($chargeDetails);
        }

        return $subscription;
    }

    /**
     * Update the subscription
     *
     * @param  array  $chargeDetails
     * @return mixed
     */
    public function updateSubscription($chargeDetails)
    {
        $currentSubscription = $this->shopService->getShop()->subscriptions()->first();
        $currentPlan = $this->getSubscriptionPlan($currentSubscription['plan']);
        $newPlan = $this->getSubscriptionPlan($chargeDetails['name']);

        if ($currentPlan == $newPlan) {
            return;
        }

        // Update the subscription plan
        $currentSubscription->update([
            'charge_id' => $chargeDetails['id'],
            'plan' => $newPlan,
            'price' => $chargeDetails['price'],
            'currency' => $chargeDetails['currency'],
            'status' => $chargeDetails['status'],
            'is_active' => $this->isSubscriptionActive($chargeDetails['status']),
            'billing_on' => $chargeDetails['billing_on'],
            'activated_on' => $chargeDetails['activated_on'],
            'trial_ends_on' => $chargeDetails['trial_ends_on'],
            'trial_days' => $chargeDetails['trial_days'],
            'cancelled_on' => $chargeDetails['cancelled_on'],
        ]);

        return $currentSubscription;
    }

    /**
     * Create the subscription
     *
     * @param  array  $chargeDetails
     * @return mixed
     */
    public function createSubscription($chargeDetails)
    {
        return $this->shopService->getShop()->subscriptions()->create([
            'charge_id' => $chargeDetails['id'],
            'plan' => $this->getSubscriptionPlan($chargeDetails['name']),
            'price' => $chargeDetails['price'],
            'currency' => $chargeDetails['currency'],
            'status' => $chargeDetails['status'],
            'is_active' => $this->isSubscriptionActive($chargeDetails['status']),
            'billing_on' => $chargeDetails['billing_on'],
            'activated_on' => $chargeDetails['activated_on'],
            'trial_ends_on' => $chargeDetails['trial_ends_on'],
            'trial_days' => $chargeDetails['trial_days'],
            'cancelled_on' => $chargeDetails['cancelled_on'],
        ]);
    }

    /**
     * Check if the subscription exists
     *
     * @return bool
     */
    public function subscriptionExists()
    {
        return $this->shopService->getShop()->subscriptions()->exists();
    }

    /**
     * Get the subscription plan
     *
     * @param  string  $name
     * @return string
     */
    public function getSubscriptionPlan($name)
    {
        $planName = 'basic';
        if (strpos($name, 'Premium') !== false) {
            $planName = 'premium';
        }
        if (strpos($name, 'premium') !== false) {
            $planName = 'premium';
        }
        if (strpos($name, 'Enterprise') !== false) {
            $planName = 'enterprise';
        }

        return $planName;
    }

    /**
     * Check if the subscription is active
     *
     * @param  string  $status
     * @return bool
     */
    public function isSubscriptionActive($status)
    {
        return $status === 'active';
    }
}
