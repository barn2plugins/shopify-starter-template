<?php

namespace Barn2App\Services;

use Gnikyt\BasicShopifyAPI\BasicShopifyAPI;
use Gnikyt\BasicShopifyAPI\Options;
use Gnikyt\BasicShopifyAPI\Session;
use Illuminate\Http\Request;

class ShopifyGraphQLService
{
    protected $shopDomain;

    protected $accessToken;

    /**
     * Holds the BasicShopifyAPI client
     *
     * @var BasicShopifyAPI
     */
    protected $client;

    public function __construct(
        Request $request,
        ShopService $shopService
    ) {
        $this->shopDomain = $shopService->getShopDomain($request);
        $this->accessToken = $shopService->getAccessToken($request);

        // Create options for the API
        $options = new Options;
        $options->setVersion('2024-10');

        // Create the client and session
        $this->client = new BasicShopifyAPI($options);
        $this->client->setSession(new Session($this->shopDomain, $this->accessToken));
    }

    public function createProduct()
    {
        $query = <<<'QUERY'
        mutation createProductMetafields($input: ProductInput!) {
            productCreate(input: $input) {
            product {
                id
                metafields(first: 3) {
                edges {
                    node {
                    id
                    namespace
                    key
                    value
                    }
                }
                }
            }
            userErrors {
                message
                field
            }
            }
        }
        QUERY;

        $variables = [
            'input' => [
                'metafields' => [
                    'namespace' => 'my_field',
                    'key' => 'liner_material',
                    'type' => 'single_line_text_field',
                    'value' => 'Synthetic Leather',
                ],
                'title' => 'Sample Product',
            ],
        ];

        // Now run your requests...
        $result = $this->client->graph($query, $variables);

        return $result;
    }

    public function getProductss()
    {
        // Define your GraphQL query
        $queryString = <<<'QUERY'
        {
            products(first: 10) {
                edges {
                    node {
                        id
                        title
                        descriptionHtml
                        featuredMedia {
                            id,
                            alt,
                            preview {
                                image {
                                    altText,
                                    id,
                                    url
                                }
                            }
                        }
                        status
                    }
                }
            }
        }
        QUERY;

        // Now run your requests...
        $result = $this->client->graph($queryString);

        if (($result['errors'] !== 'false' && $result['status'] !== 200) || ! empty($result['body']->container['errors'])) {
            return false;
        }

        return $result['body']->container['data']['products']['edges'];
    }
}
