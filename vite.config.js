import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.jsx'
            ],
            refresh: true,
        }),
        react({ include: /\.(mdx|js|jsx|ts|tsx)$/ }),
    ],
    server: {
        https: false,
        host: 'localhost',
        port: 5173,
    },
    optimizeDeps: {
        include: ['@inertiajs/inertia', '@inertiajs/inertia-react', '@shopify/polaris', '@shopify/app-bridge-react'],
    },
    resolve: {
        alias: {
            '@shopify/polaris': '@shopify/polaris',
        },
    },
    build: {
        // Ensure proper chunk sizing
        chunkSizeWarningLimit: 1000,
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['react', 'react-dom'],
                },
            },
        },
    },
});