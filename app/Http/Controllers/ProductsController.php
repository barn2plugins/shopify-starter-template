<?php

namespace Barn2App\Http\Controllers;

use Barn2App\Services\ShopifyProductService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProductsController extends Controller
{
    /**
     * The Shopify Products service
     *
     * @var ShopifyProductService
     */
    protected $productService;

    /**
     * Create a new ProductsController instance
     *
     * @return void
     */
    public function __construct(ShopifyProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Show the products page
     *
     * @return Response
     */
    public function index()
    {
        return Inertia::render('Products');
    }

    /**
     * Get the products from Shopify
     *
     * @return JsonResponse
     *
     * @throws BindingResolutionException
     */
    public function get()
    {
        $response = $this->productService->getProducts();

        if ($response === false) {
            return response()->json([
                'message' => 'Error occured.',
            ], 500);
        }

        return response()->json([
            'success'  => true,
            'products' => $response['body']->container['data']['products']['edges'],
        ], 200);
    }

    /**
     * Create a new product in Shopify
     *
     * @return JsonResponse
     *
     * @throws BindingResolutionException
     */
    public function create()
    {
        $response = $this->productService->createProduct();

        if ($response === false) {
            return response()->json([
                'message' => 'Error occured.',
            ], 500);
        }

        return response()->json([
            'success' => true,
        ], 200);
    }
}
