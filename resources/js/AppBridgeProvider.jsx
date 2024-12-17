import React from 'react';
import { AppProvider } from '@shopify/polaris';

const AppBridgeProvider = ({ children }) => {
    return (
        <AppProvider>
            {children}
        </AppProvider>
    );
};

export default AppBridgeProvider;