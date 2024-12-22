import React from 'react';
import { NavMenu } from '@shopify/app-bridge-react';
import {Text} from '@shopify/polaris';
import DevelopmentStoreNotice from './Components/DevelopmentStoreNotice';
import { usePage } from '@inertiajs/react'

export default function Layout({ children }) {
    const { isStoreActive } = usePage().props;

    return (
        <>
            <NavMenu>
                <a href="/" rel="home">
                    Home
                </a>
                <a href="/products">Products</a>
                <a href="/plans">Plans</a>
                <a href="/sample">Sample</a>
            </NavMenu>
            <div className='barn2-app-wrapper'>
                { !isStoreActive ? <DevelopmentStoreNotice /> : null }
                <div className="relative mx-auto p-6 lg:p-8">
                    <div className='text-center mb-20'>
                        <Text variant="heading3xl" as="h2">
                            Shopify Starter Template
                        </Text>
                        <div className='mt-2'>
                            <Text variant="bodyLg" as="p">
                                Welcome to your Shopify starter template. This is a great starting point of a Shopify app built with Laravel, React and Inertia.js.
                            </Text>
                        </div>
                    </div>
                    <div>{children}</div>
                </div>
            </div>
        </>
    );
}