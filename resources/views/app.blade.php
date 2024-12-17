<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="shopify-api-key" content="{{ config('shopify.api_key') }}"/>
        <title>{{ config('shopify.app_name') }}</title>
        <script src="https://cdn.shopify.com/shopifycloud/app-bridge.js"></script>
        @vite('resources/css/app.css')
        @vite('resources/js/app.jsx')
        @inertiaHead
        @yield('styles')
    </head>

    <body>
        @inertia
    </body>
</html>
