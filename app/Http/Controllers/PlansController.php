<?php

namespace App\Http\Controllers;

use App\Services\ShopifyBillingService;
use App\Services\ShopifyPlanService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

class PlansController extends Controller
{
    /**
     * The Shopify billing service
     *
     * @var ShopifyBillingService
     */
    protected $shopifyBilling;

    /**
     * The plan service
     *
     * @var ShopifyPlanService
     */
    protected $planService;

    /**
     * Create a new PlansController instance
     *
     * @return void
     */
    public function __construct(
        ShopifyBillingService $shopifyBilling,
        ShopifyPlanService $planService
    ) {
        $this->shopifyBilling = $shopifyBilling;
        $this->planService    = $planService;
    }

    /**
     * Show the plans page
     *
     * @return Response
     */
    public function index()
    {
        return Inertia::render('Plans');
    }

    /**
     * Create a new subscription with Shopify and return the redirect URL
     */
    public function create(): JsonResponse
    {
        $response = $this->shopifyBilling->createSubscription();

        if ($response === false) {
            return response()->json([
                'message' => 'Subscription creation failed',
            ], 500);
        }

        return response()->json(
            [
                'confirmation_url' => $response['body']->container['data']['appSubscriptionCreate']['confirmationUrl'],
            ]
        );
    }

    /**
     * Get the content of the plans page
     *
     * @throws BindingResolutionException
     */
    public function content(): JsonResponse
    {
        $plans = $this->planService->getPlans();

        return response()->json($plans);
    }
}
