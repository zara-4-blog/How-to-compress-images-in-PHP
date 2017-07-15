<?php

require('vendor/autoload.php');

use Zara4\API\Client;
use Zara4\API\ImageProcessing\LocalImageRequest;

// --- --- ---

/**
 * @param Client $apiClient
 * @param $inputFile
 * @param $outputFile
 */
function compressImage(Client $apiClient, $inputFile, $outputFile) {

  try {

    $originalImage = new LocalImageRequest($inputFile);

    // At this point the image to be compressed is uploaded to the Zara 4 API.
    // If you have a slow internet connection (slow upload speed) this may take a moment,
    // ... but will be significantly faster on a production server
    $processedImage = $apiClient->processImage($originalImage);

    // Download the compressed image
    $apiClient->downloadProcessedImage($processedImage, $outputFile);

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

}

// --- --- ---

// Replace with your API credentials
// You can obtain your API credentials from https://zara4.com/account/api-clients/live-credentials
// For the purpose of testing you can also use sandbox test credentials https://zara4.com/account/api-clients/test-credentials
$apiClientId     = isset($_SERVER['ZARA4_API_CLIENT_ID']) && $_SERVER['ZARA4_API_CLIENT_ID']
  ? $_SERVER['ZARA4_API_CLIENT_ID'] : "put-your-api-client-id-here";

$apiClientSecret = isset($_SERVER['ZARA4_API_CLIENT_SECRET']) && $_SERVER['ZARA4_API_CLIENT_SECRET']
  ? $_SERVER['ZARA4_API_CLIENT_SECRET'] : "put-your-api-client-secret-here";


// Create Zara 4 API client
$apiClient = new Client($apiClientId, $apiClientSecret);

// --- --- ---

compressImage($apiClient, "img/001.jpg", "compressed-001.jpg");
compressImage($apiClient, "img/002.jpg", "compressed-002.jpg");
compressImage($apiClient, "img/003.jpg", "compressed-003.jpg");