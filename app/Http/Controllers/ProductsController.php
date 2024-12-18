<?php

namespace Barn2App\Http\Controllers;

use Barn2App\Services\ShopifyGraphQLService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductsController extends Controller
{
    protected $apiClient;

    public function __construct(ShopifyGraphQLService $shopifyGraphQLService)
    {
        $this->apiClient = $shopifyGraphQLService;
    }

    public function index()
    {
        return Inertia::render('Products');
    }

    public function get()
    {
        $products = $this->apiClient->getProductss();

        return response()->json([
            'success' => true,
            'products' => $products,
        ], 200);
    }

    public function create()
    {
        $this->apiClient->createProduct();

        return response()->json([
            'success' => true,
        ], 200);
    }
}
