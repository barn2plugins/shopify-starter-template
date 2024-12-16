<?php

namespace Barn2App\Services;

use Barn2App\Exceptions\InvalidShopDomainException;
use Barn2App\Models\User;
use Illuminate\Http\Request;

class ShopService
{
    /**
     * Get specific shop
     *
     * @param  string  $shopDomain
     * @return mixed
     */
    public function getShop($shopDomain)
    {
        if (request()->user()) {
            return request()->user();
        }

        $shop = User::where(['name' => $shopDomain])->first();
        if (! $shop) {
            return false;
        }

        return $shop;
    }

    /**
     * Check if there is a store record in the database.
     *
     * @param  Request  $request  The request object.
     */
    public function checkPreviousInstallation(Request $request): bool
    {
        if (request()->user()) {
            $shop = request()->user();
        } else {
            $shop = $this->getShopFromRequest($request);
        }

        return $shop && $shop->password && ! $shop->trashed();
    }

    /**
     * Get shop data from request
     *
     * @return mixed
     *
     * @throws InvalidShopDomainException
     */
    public function getShopFromRequest(Request $request)
    {
        $shopDomain = $request->query('shop');
        if (! $shopDomain || ! filter_var("https://$shopDomain", FILTER_VALIDATE_URL)) {
            throw new InvalidShopDomainException('Shop domain invalid');
        }
        $shopDomain = strtolower($shopDomain);

        $shop = User::where('name', $shopDomain)->first();

        return $shop;
    }

    /**
     * Retrieve the Shopify shop domain from the request.
     *
     * @param  \Illuminate\Http\Request  $request  The incoming HTTP request instance.
     * @return string|null The Shopify shop domain, or null if not found.
     */
    public function getShopDomain(Request $request)
    {
        $shopDomain = false;

        if ($request->user()) {
            return $request->user()->getDomain();
        }

        if ($request->has('shop')) {
            $shopDomain = $request->get('shop');
        }

        return $shopDomain;
    }
}
