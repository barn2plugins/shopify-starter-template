<?php

namespace Barn2App\Actions;

use Barn2App\Models\User;
use Barn2App\Services\ShopService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class InstallShop
{
    protected $shopService;

    protected $request;

    public function __invoke(Request $request, ShopService $shopService)
    {
        $this->shopService = $shopService;
        $this->request = $request;

        if ($this->request->missing('code')) {
            return false;
        }

        $shop = $this->createOrRestore();

        $accessToken = $this->getAccessToken();
        if ($accessToken === false) {
            return false;
        }
        $shop->update(['password' => $accessToken]);

        return $shop;
    }

    /**
     * Create a new shop or restore a soft-deleted shop.
     *
     * @return \App\Models\User
     */
    public function createOrRestore()
    {
        // Get the shop domain
        $shopDomain = $this->shopService->getShopDomain($this->request);

        // Get the shop
        $shop = User::where('name', $shopDomain)->first();

        if (! $shop) {
            $shop = User::create([
                'name' => $shopDomain,
                'email' => 'shop@'.$shopDomain,
            ]);
        } elseif ($shop->trashed()) {
            $shop->restore();
        }

        return $shop;
    }

    /**
     * Get the access token from Shopify.
     *
     * @return string|bool
     */
    public function getAccessToken()
    {
        $shopDomain = $this->shopService->getShopDomain($this->request);
        $code = $this->request->get('code');

        $url = "https://{$shopDomain}/admin/oauth/access_token";

        $params = [
            'client_id' => config('shopify.api_key'),
            'client_secret' => config('shopify.api_secret'),
            'code' => $code,
        ];

        $response = Http::asForm()->post($url, $params);

        if ($response->failed()) {
            return false;
        }

        return $response->json('access_token');
    }
}
