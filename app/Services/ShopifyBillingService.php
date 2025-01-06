<?php

namespace App\Services;

use Illuminate\Http\Request;

class ShopifyBillingService
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
     * The plan service
     *
     * @var ShopifyPlanService
     */
    protected $planService;

    /**
     * The GraphQL client
     *
     * @var ShopifyGraphQLService
     */
    protected $graphQlClient;

    /**
     * Create a new ShopifyBillingService instance
     *
     * @return void
     */
    public function __construct(
        Request $request,
        ShopService $shopService,
        ShopifyPlanService $planService,
        ShopifyGraphQLService $graphQlClient
    ) {
        $this->request       = $request;
        $this->shopService   = $shopService;
        $this->planService   = $planService;
        $this->graphQlClient = $graphQlClient;
    }

    /**
     * Create a new subscription with Shopify
     *
     * @return mixed
     */
    public function createSubscription()
    {
        if (! $this->request->has('plan')) {
            return false;
        }

        $plan          = $this->planService->getPlan($this->request->get('plan'));
        $billingPeriod = $this->request->get('billing_period');

        $query = <<<'GRAPHQL'
            mutation AppSubscriptionCreate($
                name: String!, 
                $lineItems: [AppSubscriptionLineItemInput!]!, 
                $returnUrl: URL!,
                $trialDays: Int,
                $test: Boolean
            ) {
                appSubscriptionCreate(
                    name: $name, 
                    returnUrl: $returnUrl, 
                    lineItems: $lineItems,
                    trialDays: $trialDays,
                    test: $test,
                ) {
                userErrors {
                    field
                    message
                }
                appSubscription {
                    id
                }
                confirmationUrl
                }
            }
        GRAPHQL;

        $variables = [
            'name'      => $plan['title'],
            'returnUrl' => route('subscription.create'),
            'trialDays' => 14,
            'test'      => true,
            'lineItems' => [
                [
                    'plan' => [
                        'appRecurringPricingDetails' => [
                            'price' => [
                                'amount'       => $this->getBillingPrice($plan, $billingPeriod),
                                'currencyCode' => 'USD',
                            ],
                            'interval' => $this->getBillingInterval($billingPeriod),
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->graphQlClient->query($query, $variables);

        if ($response['errors'] === false && $response['status'] === 200) {
            return $response;
        }

        return false;
    }

    /**
     * Get the billing interval for the plan
     *
     * @param  string  $billingPeriod
     * @return string
     */
    public function getBillingInterval($billingPeriod)
    {
        $interval = 'EVERY_30_DAYS';

        if ($billingPeriod === 'annual') {
            $interval = 'ANNUAL';
        }

        return $interval;
    }

    /**
     * Get the billing price for the plan
     *
     * @param  array  $plan
     * @param  string  $billingPeriod
     * @return mixed
     */
    public function getBillingPrice($plan, $billingPeriod)
    {
        $price = $plan['price']['sale'];

        if ($billingPeriod === 'annual') {
            $price = $plan['annualPrice']['sale'];
        }

        return $price;
    }
}
