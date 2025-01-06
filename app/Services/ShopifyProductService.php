<?php

namespace App\Services;

class ShopifyProductService
{
    /**
     * The GraphQL client
     *
     * @var ShopifyGraphQLService
     */
    protected $graphQlClient;

    /**
     * Create a new ShopifyProductService instance
     *
     * @return void
     */
    public function __construct(ShopifyGraphQLService $shopifyGraphQLService)
    {
        $this->graphQlClient = $shopifyGraphQLService;
    }

    /**
     * Get the products from Shopify
     *
     * @return array
     */
    public function getProducts()
    {
        // Define your GraphQL query
        $query = <<<'QUERY'
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

        $response = $this->graphQlClient->query($query);

        if ($response['errors'] === false && $response['status'] === 200) {
            return $response;
        }

        return false;
    }

    /**
     * Create a new product in Shopify
     *
     * @return array
     */
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
                    'key'       => 'liner_material',
                    'type'      => 'single_line_text_field',
                    'value'     => 'Synthetic Leather',
                ],
                'title' => 'Sample Product',
            ],
        ];

        $response = $this->graphQlClient->query($query, $variables);

        if ($response['errors'] === false && $response['status'] === 200) {
            return $response;
        }

        return false;
    }
}
