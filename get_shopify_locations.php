<?php

require __DIR__.'/vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Shopify API credentials
$shopDomain = $_ENV['SHOPIFY_SHOP_DOMAIN'];
$accessToken = $_ENV['SHOPIFY_ACCESS_TOKEN'];
$apiVersion = $_ENV['SHOPIFY_API_VERSION'];

// Make API request to get locations
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://{$shopDomain}/admin/api/{$apiVersion}/locations.json");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "X-Shopify-Access-Token: {$accessToken}",
    "Content-Type: application/json"
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Process response
if ($httpCode == 200) {
    $data = json_decode($response, true);
    echo "Available Shopify Locations:\n";
    echo "===========================\n";
    
    foreach ($data['locations'] as $location) {
        echo "Location Name: " . $location['name'] . "\n";
        echo "Location ID: " . $location['id'] . "\n";
        echo "Active: " . ($location['active'] ? 'Yes' : 'No') . "\n";
        echo "----------------------------\n";
    }
    
    echo "\nAdd the primary location ID to your .env file as SHOPIFY_LOCATION_ID\n";
} else {
    echo "Error fetching locations: HTTP Code {$httpCode}\n";
    echo $response;
}
