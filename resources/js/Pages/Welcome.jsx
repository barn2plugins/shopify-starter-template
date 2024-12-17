import React from 'react';
import { Head } from '@inertiajs/react';
import { EmptyState } from '@shopify/polaris';

const Welcome = () => {
    return (
        <>
            <Head title="Welcome" />
            <div className="bg-white rounded-lg shadow mx-auto flex justify-center max-w-lg">
                <EmptyState
                    heading="Manage your products"
                    action={{
                        content: 'Create a product',
                        url: 'https://help.shopify.com',
                    }}
                    secondaryAction={{
                        content: 'View all products',
                        url: '/products'
                    }}
                    image="https://cdn.shopify.com/s/files/1/0262/4071/2726/files/emptystate-files.png"
                >
                    <p>This starter templates provides a products page where it retrieves all the products from your shop using GraphQL and you can create a random product as well by clicking on the create button.</p>
                </EmptyState>
            </div>
        </>
    );
}

export default Welcome;