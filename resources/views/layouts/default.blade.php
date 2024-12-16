<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="shopify-api-key" content="{{ config('shopify.api_key') }}"/>
        <script src="https://cdn.shopify.com/shopifycloud/app-bridge.js"></script>

        <title>{{ config('shopify.app_name') }}</title>
        @yield('styles')
    </head>

    <body>
        <div class="app-wrapper">
            <div class="app-content">
                <main role="main">
                    @yield('content')
                </main>
            </div>
        </div>

        @include('partials.token_handler')
        @yield('scripts')
    </body>
</html>
