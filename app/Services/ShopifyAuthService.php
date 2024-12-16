<?php

namespace Barn2App\Services;

use Barn2App\Models\User;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

class ShopifyAuthService
{
    /**
     * The service responsible for decoding and validating Shopify tokens.
     *
     * @var \Barn2App\Services\ShopifyTokenService
     */
    private $tokenService;

    /**
     * The service responsible for retrieving shop details
     *
     * @var \Barn2App\Services\ShopService
     */
    private $shopService;

    /**
     * The authentication guard instance for managing user sessions.
     *
     * @var \Illuminate\Contracts\Auth\Guard
     */
    private $auth;

    /**
     * Create a new instance of the Shopify middleware.
     *
     * @param  \App\Services\ShopifyTokenService  $tokenService  The token service for Shopify token operations.
     * @param  \Illuminate\Contracts\Auth\Guard  $auth  The authentication guard instance.
     */
    public function __construct(
        ShopifyTokenService $tokenService,
        ShopService $shopService,
        Guard $auth
    ) {
        $this->tokenService = $tokenService;
        $this->shopService = $shopService;
        $this->auth = $auth;
    }

    /**
     * Handle the redirect to the token authentication route.
     *
     * This method processes the incoming request by verifying the HMAC
     * and other query parameters, then filters out sensitive or unnecessary
     * parameters before redirecting to the token authentication route.
     *
     * @param  \Illuminate\Http\Request  $request  The incoming HTTP request object.
     * @return \Illuminate\Http\RedirectResponse The redirect response to the authentication route.
     */
    public function tokenRedirect(Request $request)
    {
        // At this point the HMAC and other details are verified already, filter it out
        $path = $request->path();
        $target = Str::start($path, '/');
        $shopDomain = $this->shopService->getShopDomain($request);

        if ($request->query()) {
            $filteredQuery = Collection::make($request->query())->except([
                'hmac',
                'locale',
                'new_design_language',
                'timestamp',
                'session',
                'shop',
            ]);

            if ($filteredQuery->isNotEmpty()) {
                $target .= '?'.http_build_query($filteredQuery->toArray());
            }
        }

        return Redirect::route(
            'authenticate.token',
            [
                'shop' => $shopDomain,
                'target' => $target,
                'host' => $request->get('host'),
                'locale' => $request->get('locale'),
            ]
        );
    }

    /**
     * Login shop using JWT token
     */
    public function loginShopFromToken(string $token): bool
    {
        $tokenData = $this->tokenService->decodeToken($token);

        if (! isset($tokenData['shop'])) {
            return false;
        }

        // Get the shop
        $shop = $this->shopService->getShop($tokenData['shop']);

        if (! $shop) {
            return false;
        }

        $this->auth->login($shop);

        return true;
    }

    /**
     * Redirect to install route.
     */
    public function installRedirect(): RedirectResponse
    {
        return Redirect::route(
            'authenticate',
            [
                'shop' => request('shop'),
                'host' => request('host'),
                'locale' => request('locale'),
            ]
        );
    }
}
