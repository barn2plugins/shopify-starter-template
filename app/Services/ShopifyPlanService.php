<?php

namespace Barn2App\Services;

use Carbon\Carbon;

class ShopifyPlanService
{
    /**
     * The shop service
     *
     * @var ShopService
     */
    protected $shopService;

    /**
     * Create a new ShopifyPlanService instance
     *
     * @return void
     */
    public function __construct(ShopService $shopService)
    {
        $this->shopService = $shopService;
    }

    /**
     * Get all plans
     */
    public function getPlans(): array
    {
        return [
            'title'    => 'Billing Plans',
            'subtitle' => 'Choose a pricing plan that meets your needs!',
            'plans'    => [
                'basic' => [
                    'id'          => 1,
                    'name'        => 'basic',
                    'title'       => 'Basic Plan',
                    'description' => 'Basic features',
                    'price'       => [
                        'regular' => 14.99,
                        'sale'    => 9.99,
                    ],
                    'annualPrice' => [
                        'regular' => 119.99,
                        'sale'    => 89.99,
                    ],
                    'features' => [
                        [
                            'name'      => '3 types of bundles',
                            'available' => true,
                        ],
                        [
                            'name'      => '10 products per bundle',
                            'available' => true,
                        ],
                        [
                            'name'      => 'Revenue tracking',
                            'available' => false,
                        ],
                        [
                            'name'      => 'Analytics',
                            'available' => false,
                        ],
                        [
                            'name'      => 'Post purchase upsells',
                            'available' => false,
                        ],
                    ],
                ],
                'premium' => [
                    'id'          => 2,
                    'name'        => 'premium',
                    'title'       => 'Premium Plan',
                    'description' => 'Exclusive premium features',
                    'price'       => [
                        'regular' => 39.99,
                        'sale'    => 19.99,
                    ],
                    'annualPrice' => [
                        'regular' => 229.99,
                        'sale'    => 99.99,
                    ],
                    'features' => [
                        [
                            'name'      => '3 types of bundles',
                            'available' => true, ],
                        [
                            'name'      => '10 products per bundle',
                            'available' => true,
                        ],
                        [
                            'name'      => 'Revenue tracking',
                            'available' => true,
                        ],
                        [
                            'name'      => 'Analytics',
                            'available' => false,
                        ],
                        [
                            'name'      => 'Post purchase upsells',
                            'available' => false,
                        ],
                    ],
                ],
                'enterprise' => [
                    'id'          => 3,
                    'name'        => 'enterprise',
                    'title'       => 'Enterprise Plan',
                    'description' => 'All features included',
                    'price'       => [
                        'regular' => 199.99,
                        'sale'    => 99.99, ],
                    'annualPrice' => [
                        'regular' => 1499.99,
                        'sale'    => 799.99,
                    ],
                    'features' => [
                        [
                            'name'      => '3 types of bundles',
                            'available' => true,
                        ],
                        [
                            'name'      => '10 products per bundle',
                            'available' => true,
                        ],
                        [
                            'name'      => 'Revenue tracking',
                            'available' => true,
                        ],
                        [
                            'name'      => 'Analytics',
                            'available' => true,
                        ],
                        [
                            'name'      => 'Post purchase upsells',
                            'available' => true,
                        ],
                    ],
                ],
            ],
            'current_plan'     => $this->getCurrentSubscribedPlan(),
            'billing_interval' => $this->determineIntervalPeriodByDates(),
        ];
    }

    /**
     * Get specific plan
     *
     * @param  mixed  $plan
     */
    public function getPlan($plan): array
    {
        $plans = $this->getPlans();

        if (empty($plans['plans'][$plan])) {
            return [];
        }

        return $plans['plans'][$plan];
    }

    /**
     * Get the current subscribed plan
     */
    public function getCurrentSubscribedPlan(): string
    {
        $plan = 'null';
        $shop = $this->shopService->getShop();
        if ($shop) {
            $subscription = $shop->subscriptions()->first();
            if ($subscription && $subscription->isActive()) {
                $plan = $subscription->plan;
            }
        }

        return $plan;
    }

    /**
     * Determine the interval period by dates
     */
    public function determineIntervalPeriodByDates(): string
    {
        $shop     = $this->shopService->getShop();
        $interval = 'monthly';

        if (! $shop) {
            return $interval;
        }

        $subscription = $shop->subscriptions()->first();

        if ($subscription && $subscription->isActive()) {
            // Parse dates using Carbon
            $trialEndsOn        = Carbon::parse($subscription['trial_ends_on']);
            $billingOn          = Carbon::parse($subscription['billing_on']);
            $differenceInMonths = ceil($trialEndsOn->diffInMonths($billingOn));

            // Determine subscription type based on the difference
            if ($differenceInMonths === 1.0) {
                $interval = 'monthly';
            } elseif ($differenceInMonths === 12.0) {
                $interval = 'annual';
            }
        }

        return $interval;
    }
}
