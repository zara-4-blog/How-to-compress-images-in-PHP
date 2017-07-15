<?php

require('vendor/autoload.php');

// Replace with your API credentials
// You can obtain your API credentials from https://zara4.com/account/api-clients/live-credentials
// For the purpose of testing you can also use sandbox test credentials https://zara4.com/account/api-clients/test-credentials
$apiClientId = isset($_SERVER['ZARA4_API_CLIENT_ID']) && $_SERVER['ZARA4_API_CLIENT_ID']
  ? $_SERVER['ZARA4_API_CLIENT_ID'] : "put-your-api-client-id-here";

$apiClientSecret = isset($_SERVER['ZARA4_API_CLIENT_SECRET']) && $_SERVER['ZARA4_API_CLIENT_SECRET']
  ? $_SERVER['ZARA4_API_CLIENT_SECRET'] : "put-your-api-client-secret-here";


// Create Zara 4 API client
$apiClient = new Zara4\API\Client($apiClientId, $apiClientSecret);

// --- --- ---

try {
  $originalImage  = new Zara4\API\ImageProcessing\RemoteImageRequest("https://github.com/zara-4-blog/how-to-compress-images-in-php/raw/master/img/001.jpg");
  $processedImage = $apiClient->processImage($originalImage);
  $apiClient->downloadProcessedImage($processedImage, "compressed.jpg");

  echo "Percentage Saving: " . number_format($processedImage->percentageSaving(), 2) . "%\n";
}

// Out of quota
catch (\Zara4\API\ImageProcessing\QuotaLimitException $e) {

  // Either use test API sandbox credentials (see https://zara4.com/account/api-clients/test-credentials)
  // or view are compression packages at https://zara4.com/pricing
  die("Compression Failed - Out of compression quota");
}

// Submitted image it too large
catch (\Zara4\API\ImageProcessing\FileSizeTooLargeException $e) {
  die("Compression Failed - Image too large");
}

// Submitted file is not an image
catch (\Zara4\API\ImageProcessing\InvalidImageFormatException $e) {
  die("Compression Failed - Not a recognised image format (supports jpg, png, gif and svg)");
}
