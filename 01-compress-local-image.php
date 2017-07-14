<?php

require('vendor/autoload.php');

use Zara4\API\Client;
use Zara4\API\ImageProcessing\LocalImageRequest;

// --- --- ---

// You can obtain your API credentials from
// https://zara4.com/account/api-clients/live-credentials
$apiClientId     = "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";
$apiClientSecret = "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";

// Create Zara 4 API client
$apiClient = new Client($apiClientId, $apiClientSecret);

// Compress the image
$originalImage  = new LocalImageRequest("img/001.jpg");
$processedImage = $apiClient->processImage($originalImage);

// Download compressed image
$apiClient->downloadProcessedImage($processedImage, "compressed.jpg");