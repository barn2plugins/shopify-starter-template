import React, { useState } from 'react';
import { EmptyState } from '@shopify/polaris';
import axios from 'axios';
import Layout from '../Layout';

const Welcome = () => {
    const [ loading, setLoading ] = useState(false);

    const handleCreateProduct = async (e) => {
        e.preventDefault();
        setLoading(true);

        try {
            const response = await axios.post('/products/create');
            if (  response.status === 200 ) {
                setLoading(false);
                shopify.toast.show(
                    'Product "Sample Product" has been created', 
                    {
                        duration: 3000
                    }
                )
            }
        } catch {

        } finally {
            
        }
    }

    return (
        <Layout>
            <div className="bg-white rounded-lg shadow mx-auto flex justify-center max-w-lg">
                <EmptyState
                    heading="Manage your products"
                    action={{
                        content: 'Create a product',
                        loading,
                        onAction: handleCreateProduct
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
        </Layout>
    );
}

export default Welcome;