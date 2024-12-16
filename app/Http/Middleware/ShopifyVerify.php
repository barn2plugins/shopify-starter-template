<?php

namespace Barn2App\Http\Middleware;

use Barn2App\Actions\Hmac;
use Barn2App\Exceptions\HttpException;
use Barn2App\Exceptions\SignatureVerificationException;
use Barn2App\Services\ShopifyAuthService;
use Barn2App\Services\ShopService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class ShopifyVerify
{
    /**
     * Responsible for shopify auth services
     *
     * @var \Barn2App\Services\ShopifyAuthService
     */
    private $shopifyAuth;

    /**
     * The shop service.
     *
     * @var \Barn2App\Services\ShopService
     */
    private $shopService;

    /**
     * Class constructor to initialize dependencies.
     *
     * This constructor initializes the Shopify authentication service
     * and shop service to handle Shopify-related logic, such as
     * authentication and shop management.
     *
     * @param  \App\Services\ShopifyAuthService  $shopifyAuth  The service responsible for Shopify authentication.
     * @param  \App\Services\ShopService  $shopService  The service responsible for handling shop-related logic.
     */
    public function __construct(
        ShopifyAuthService $shopifyAuth,
        ShopService $shopService,
    ) {
        $this->shopifyAuth = $shopifyAuth;
        $this->shopService = $shopService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Hmac::verify($request) === false) {
            throw new SignatureVerificationException('Unable to verify signature.');
        }

        // Continue if current route is an authenticate route
        if (Str::contains($request->getRequestUri(), ['/authenticate'])) {
            return $next($request);
        }

        $token = $this->getAccessTokenFromRequest($request);

        if ($token === null) {
            // Check if there is a store record in the database
            return $this->shopExists($request)
                // Shop exists but the token not available, let's get the token
                ? $this->handleMissingToken($request)
                // Shop does not exist
                : $this->handleInvalidShop();
        }

        $loginResult = $this->shopifyAuth->loginShopFromToken($token);

        if (! $loginResult) {
            return $this->handleInvalidShop();
        }

        return $next($request);
    }

    /**
     * Get the token from request (if available).
     *
     * @param  Request  $request  The request object.
     */
    protected function getAccessTokenFromRequest(Request $request): ?string
    {
        return $request->get('token');
    }

    /**
     * Check if there is a store record in the database.
     *
     * @param  Request  $request  The request object.
     */
    protected function shopExists(Request $request): bool
    {
        return $this->shopService->checkPreviousInstallation($request);
    }

    /**
     * Handle missing token.
     *
     * @param  Request  $request  The request object.
     * @return mixed
     *
     * @throws HttpException If an AJAX/JSON request.
     */
    protected function handleMissingToken(Request $request)
    {
        return $this->shopifyAuth->tokenRedirect($request);
    }

    /**
     * Handle an invalid or expired token.
     *
     * @param  Request  $request  The request object.
     * @param  AssertionFailedException  $e  The assertion failure exception.
     * @return mixed
     *
     * @throws HttpException If an AJAX/JSON request.
     */

    /**
     * Handle a shop that is not installed or it's data is invalid.
     *
     * @return mixed
     */
    protected function handleInvalidShop()
    {
        return $this->shopifyAuth->installRedirect();
    }
}
