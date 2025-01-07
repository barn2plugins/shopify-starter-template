<?php

namespace App\Http\Controllers;

use App\Actions\InstallShop;
use App\Exceptions\MissingShopDomainException;
use App\Services\ShopifyGraphQLService;
use App\Services\ShopifyWebhookService;
use App\Services\ShopService;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class AuthController extends Controller
{
    /**
     * Service to handle operations related to Shopify shops.
     *
     * @var \App\Services\ShopService
     */
    private $shopService;

    /**
     * Service to handle operations related to Shopify shops.
     *
     * @var \App\Services\ShopifyGraphQLService
     */
    private $graphQlClient;

    /**
     * Service to handle operations related to Shopify webhooks.
     *
     * @var \App\Services\ShopifyWebhookService
     */
    private $webhookService;

    /**
     * Constructor to initialize the shop service.
     *
     * This constructor accepts an instance of the `ShopService` class and assigns
     * it to the `shopService` property for use within the class. The `ShopService`
     * provides methods to handle shop-related functionality and business logic.
     *
     * @param  \App\Services\ShopService  $shopService  The service instance to handle shop operations.
     */
    public function __construct(
        ShopService $shopService,
        ShopifyWebhookService $webhookService,
        ShopifyGraphQLService $graphQlClient
    ) {
        $this->shopService    = $shopService;
        $this->webhookService = $webhookService;
        $this->graphQlClient  = $graphQlClient;
    }

    public function authenticate(Request $request, InstallShop $installShop)
    {
        if ($request->missing('shop') && ! $request->user()) {
            // One or the other is required to authenticate a shop
            throw new MissingShopDomainException('No authenticated user or shop domain');
        }

        // run action to install shop
        $shop       = $installShop($request, $this->shopService, $this->graphQlClient);
        $shopDomain = $this->shopService->getShopDomain($request);

        if (! $shop) {
            return View::make(
                'auth.redirect',
                [
                    'apiKey' => config('shopify.api_key'),
                    'url'    => $this->getShopifyAuthorizeURI($request),
                ]
            );
        }

        // Register webhooks with Shopify
        $this->webhookService->register($shop);

        return Redirect::route(
            'home',
            [
                'shop'   => $shopDomain,
                'host'   => $request->get('host'),
                'locale' => $request->get('locale'),
            ]
        );
    }

    public function token(Request $request)
    {
        $request->session()->reflash();

        $redirectData = $this->getTokenRedirectData($request);

        return View::make(
            'auth.token',
            [
                'shopDomain' => $redirectData['shopDomain'],
                'target'     => $redirectData['cleanTarget'],
            ]
        );
    }

    protected function getShopifyAuthorizeURI(Request $request)
    {
        // Get the shop domain
        $shopDomain = $this->shopService->getShopDomain($request);

        $query = http_build_query([
            'client_id'    => config('shopify.api_key'),
            'redirect_uri' => route('authenticate'),
            'scope'        => config('shopify.api_scopes'),
            'state'        => csrf_token(),
        ]);

        return sprintf(
            'https://%s/admin/oauth/authorize?%s',
            $shopDomain,
            $query
        );
    }

    /**
     * Get token redirect data for Shopify authentication.
     *
     * This method processes the request query parameters to generate a clean target URL
     * with relevant Shopify details, such as the shop domain, host, and locale.
     * It ensures the `token` parameter is excluded and prepares the data needed
     * for token redirection.
     *
     * @param  \Illuminate\Http\Request  $request  The HTTP request containing query parameters.
     * @return array An array containing:
     *               - `shopDomain`: The Shopify shop domain extracted from the request.
     *               - `cleanTarget`: The sanitized target URL with required parameters.
     */
    public function getTokenRedirectData(Request $request)
    {
        $shopDomain  = $this->shopService->getShopDomain($request);
        $target      = $request->query('target');
        $query       = parse_url($target, PHP_URL_QUERY);
        $cleanTarget = $target;

        if ($query) {
            $params           = Util::parseQueryString($query);
            $params['shop']   = $shopDomain;
            $params['host']   = $request->get('host');
            $params['locale'] = $request->get('locale');
            unset($params['token']);

            $cleanTarget = trim(explode('?', $target)[0].'?'.http_build_query($params), '?');
        } else {
            $params = [
                'shop'   => $shopDomain,
                'host'   => $request->get('host'),
                'locale' => $request->get('locale'),
            ];
            $cleanTarget = trim(explode('?', $target)[0].'?'.http_build_query($params), '?');
        }

        return [
            'shopDomain'  => $shopDomain,
            'cleanTarget' => $cleanTarget,
        ];
    }
}
