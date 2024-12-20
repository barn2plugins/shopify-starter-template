<?php

namespace Barn2App\Http\Middleware;

use Barn2App\Services\ShopService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;

class IframeProtection
{

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $shopService = new ShopService();

        $shop = $shopService->getShopFromRequest($request);

        $shop = Cache::remember(
            'frame-ancestors_'.$shop['name'],
            now()->addMinutes(20),
            function () use ($shop) {
                return $shop;
            }
        );

        $domain = $shop
            ? $shop['name']
            : '*.myshopify.com';

        $iframeAncestors = "frame-ancestors https://$domain https://admin.shopify.com";

        $response->headers->set(
            'Content-Security-Policy',
            $iframeAncestors
        );
        
        return $response;
    }
}
