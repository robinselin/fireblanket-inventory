# Updating Shopify API Permissions

## Problem
Your Shopify integration is failing with the error: `[API] This action requires merchant approval for write_inventory scope.`

## Solution
You need to update your Shopify API access token to include the `write_inventory` scope.

## Step-by-Step Guide

### 1. Log in to your Shopify Partner Dashboard
- Go to [partners.shopify.com](https://partners.shopify.com) and log in

### 2. Navigate to your App
- Go to "Apps" in the left sidebar
- Find and click on your Fire Blanket app

### 3. Update App Scopes
- Go to "App setup" or "Settings"
- Find the "Access scopes" or "API permissions" section
- Add the `write_inventory` scope to your app's required permissions
- The full list of scopes should include:
  - `read_products`
  - `read_inventory`
  - `write_inventory` (add this one)

### 4. Generate a New Access Token
- After updating the scopes, you'll need to generate a new access token
- This may require reinstalling the app in your Shopify store
- Follow the prompts to complete this process
- Copy the new access token

### 5. Update Your .env File
- Replace your current `SHOPIFY_ACCESS_TOKEN` in your `.env` file with the new token:

```
SHOPIFY_ACCESS_TOKEN=your_new_token_here
```

### 6. Clear Configuration Cache
- Run this command in your terminal:
```
php artisan config:clear
```

### 7. Test the Integration
- Run the Shopify sync script again:
```
php trigger_shopify_sync.php
```

## Verification
After completing these steps, check the Laravel logs to ensure there are no more permission errors:
```
tail -n 50 storage/logs/laravel.log
```

You should see successful inventory updates without the permission error messages.
