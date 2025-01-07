# Barn2 Shopify Starter Template
This repository can be used as a template. Click the **'Use this template'** button at the top and create a new repository for a new app.

Now from the app repository, clone the template into your local development area and cd into.

Install composer
```sh
composer install
```
Install npm packages
```sh
npm install
```
Build assets file
```sh
npm run dev
```
Copy .env file from .env.example
```sh
cp .env.example .env
```
Setup the below env variables
```sh
APP_URL=https://your-app-url.test
DB_CONNECTION=mariadb
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=shopify_starter_template
DB_USERNAME=root
DB_PASSWORD=password
```
Now generate Laravel application key
```sh
php artisan key:generate
```
Once all are setup, run the migrations
```sh
php artisan migrate
```
At this point, the Laravel application should be up and running. 

#### Create Shopify App
This app leverage the use of Shopify CLI. Shopify CLI helps to create, build, and deploy Shopify apps from CLI. If Shopify CLI isn't installed yet, please follow the installation [guidelines here](https://shopify.dev/docs/api/shopify-cli).   

Now create this code repository as a Shopify app by running the command
```
shopify app build
```
This will ask **Create this project as a new app on Shopify?**, select 
**Yes, create it as a new app**

Then it will ask for enter an app name, enter an app name and submit.   
After that, the app will be created on your Shopify partner dashboard.   
Go to https://partners.shopify.com/ and navigate to the 'All apps' page and you will see the app you just created.   
Get the `Client ID` and `Client secret` and update the `.env` file with the below values
```
SHOPIFY_APP_NAME='{the-app-name}'
SHOPIFY_API_KEY=put-client-id
SHOPIFY_API_SECRET=put-client-secret
SHOPIFY_API_SCOPES=read_products,write_products,write_script_tags
SHOPIFY_REDIRECT_URI='https://shopify-starter-template.test/authenticate'
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE="none"
```

Go to the Shopify app configuration page and update the   
`App URL` to the app URL `https://your-app-site.com/` and   
`Allowed redirection URL(s)` to `https://your-app-site.com/authenticate`   

**Now we are all set up for the app and ready to install to a store.**
