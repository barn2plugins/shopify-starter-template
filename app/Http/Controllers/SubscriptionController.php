<?php

namespace App\Http\Controllers;

use App\Services\ShopifyGraphQLService;
use App\Services\ShopService;
use Carbon\Carbon;
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
     * The Shopify GraphQL API service
     *
     * @var ShopifyGraphQLService
     */
    protected $graphQlClient;

    /**
     * Create a new SubscriptionController instance
     *
     * @return void
     */
    public function __construct(
        Request $request,
        ShopService $shopService,
        ShopifyGraphQLService $graphQlClient
    ) {
        $this->request       = $request;
        $this->shopService   = $shopService;
        $this->graphQlClient = $graphQlClient;
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

        $query = <<<'QUERY'
            query GetRecurringApplicationCharges {
                currentAppInstallation {
                    activeSubscriptions {
                        id
                        name
                        status
                        createdAt
                        currentPeriodEnd
                        trialDays
                        lineItems {
                            id
                            plan {
                                pricingDetails {
                                    __typename
                                    ... on AppRecurringPricing {
                                        price {
                                            amount
                                            currencyCode
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        QUERY;

        $response = $this->graphQlClient->query($query);

        if ($response['status'] !== 200) {
            return redirect()->route('home');
        }

        $this->createOrUpdateSubscription($response['body']->container['data']['currentAppInstallation']['activeSubscriptions'][0]);

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
        $currentPlan         = $this->getSubscriptionPlan($currentSubscription['plan']);
        $newPlan             = $this->getSubscriptionPlan($chargeDetails['name']);

        if ($currentPlan == $newPlan) {
            return;
        }

        // Update the subscription plan
        $currentSubscription->update([
            'charge_id'     => $this->extractChargeID($chargeDetails['id']),
            'plan'          => $newPlan,
            'price'         => $chargeDetails['lineItems'][0]['plan']['pricingDetails']['price']['amount'],
            'currency'      => $chargeDetails['lineItems'][0]['plan']['pricingDetails']['price']['currencyCode'],
            'status'        => strtolower($chargeDetails['status']),
            'is_active'     => $this->isSubscriptionActive($chargeDetails['status']),
            'billing_on'    => Carbon::parse($chargeDetails['currentPeriodEnd'])->toDateTimeString(),
            'activated_on'  => Carbon::parse($chargeDetails['createdAt'])->toDateTimeString(),
            'trial_ends_on' => (Carbon::parse($chargeDetails['createdAt'])->addDays($chargeDetails['trialDays']))->toDateTimeString(),
            'trial_days'    => $chargeDetails['trialDays'],
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
            'charge_id'     => $this->extractChargeID($chargeDetails['id']),
            'plan'          => $this->getSubscriptionPlan($chargeDetails['name']),
            'price'         => $chargeDetails['lineItems'][0]['plan']['pricingDetails']['price']['amount'],
            'currency'      => $chargeDetails['lineItems'][0]['plan']['pricingDetails']['price']['currencyCode'],
            'status'        => strtolower($chargeDetails['status']),
            'is_active'     => $this->isSubscriptionActive($chargeDetails['status']),
            'billing_on'    => Carbon::parse($chargeDetails['currentPeriodEnd'])->toDateTimeString(),
            'activated_on'  => Carbon::parse($chargeDetails['createdAt'])->toDateTimeString(),
            'trial_ends_on' => (Carbon::parse($chargeDetails['createdAt'])->addDays($chargeDetails['trialDays']))->toDateTimeString(),
            'trial_days'    => $chargeDetails['trialDays'],
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
        return $status === 'ACTIVE';
    }

    /**
     * Extracts the numeric charge ID from a given string.
     *
     * @param  string  $id  The input string containing the charge ID.
     * @return int|false The extracted charge ID as an integer, or false if no match is found.
     */
    public function extractChargeID($id)
    {
        if (preg_match('/(\d+)$/', $id, $matches)) {
            $number = $matches[1];

            return $number;
        }

        return false;
    }
}
