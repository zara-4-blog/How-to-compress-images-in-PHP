<?php
require('vendor/autoload.php');

use Zara4\API\Client;
use Zara4\API\ImageProcessing\LocalImageRequest;

?>
<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
  <script
    src="https://code.jquery.com/jquery-1.12.4.min.js"
    integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ="
    crossorigin="anonymous"></script>
</head>
<body>

  <div class="container">
    <div class="row">
      <div class="col-xs-12 col-sm-10 col-md-8 col-lg-6 col-sm-offset-1 col-md-offset-2 col-lg-offset-3">


        <div class="text-center">
          <h1>Upload and Compress</h1>
        </div>

        <hr/>

        <div class="well">
          <?php if (isset($_POST["submit"])): ?>
            <?php

            // Check we got the inputs we need
            if (!file_exists($_FILES['file-to-upload']['tmp_name']) || !is_uploaded_file($_FILES['file-to-upload']['tmp_name'])) { die('Bad Request - No file uploaded'); }
            if (!isset($_POST["api-client-id"]) || $_POST["api-client-id"] == "") { die('Bad Request - Missing Zara 4 API client id'); }
            if (!isset($_POST["api-client-secret"]) || $_POST["api-client-secret"] == "") { die('Bad Request - Missing Zara 4 API client secret'); }

            // --- --- ---

            $uploadedFilePath = $_FILES["file-to-upload"]["tmp_name"];

            // You can obtain your API credentials from
            // https://zara4.com/account/api-clients/live-credentials
            $apiClientId     = $_POST["api-client-id"];
            $apiClientSecret = $_POST["api-client-secret"];

            // Create Zara 4 API client
            $apiClient = new Client($apiClientId, $apiClientSecret);

            // --- --- ---

            //
            // Compress the image
            //
            try {
              $originalImage  = new LocalImageRequest($uploadedFilePath);
              $processedImage = $apiClient->processImage($originalImage);
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
              die("Compression Failed - Not a recognised image format (supports jp, png, gif and svg)");
            }

            // --- --- ---

            // Download compressed image
            $apiClient->downloadProcessedImage($processedImage, "compressed.jpg");

            ?>

            <div>
              <table class="table">
                <tr>
                  <th>Percentage Saving:</th>
                  <td><?php echo number_format($processedImage->percentageSaving(), 2) ?>%</td>
                </tr>
                <tr>
                  <th>Original file size:</th>
                  <td><?php echo $processedImage->originalFileSize() ?></td>
                </tr>
                <tr>
                  <th>Compressed file size:</th>
                  <td><?php echo $processedImage->compressedFileSize() ?></td>
                </tr>
                <tr>
                  <th>Bytes saved:</th>
                  <td><?php echo $processedImage->originalFileSize() - $processedImage->compressedFileSize() ?></td>
                </tr>
              </table>
            </div>

            <div class="text-center">
              <img style="max-width: 500px; max-height: 500px" src="compressed.jpg" />
            </div>

            <hr/>

            <div class="text-center">
              <a class="btn btn-default" href="">Reset</a>
            </div>

          <?php else: ?>

            <div class="text-center">
              <p style="margin-bottom: 20px">
                You can obtain your API credentials by clicking <a target="_blank" href="https://zara4.com/account/api-clients/live-credentials">here</a>
              </p>
            </div>

            <form method="post" enctype="multipart/form-data" id="form">

              <table class="table">

                <!-- API Client Id -->
                <tr>
                  <td><label for="api-client-id">API Client Id</label></td>
                  <td><input type="text" class="form-control" name="api-client-id" id="api-client-id" placeholder="API Client Id" /></td>
                </tr>

                <!-- API Client Secret -->
                <tr>
                  <td><label for="api-client-secret">API Client Secret</label></td>
                  <td><input type="text" class="form-control" name="api-client-secret" id="api-client-secret" placeholder="API Client Secret" /></td>
                </tr>

                <!-- File upload -->
                <tr>
                  <td><label for="file-to-upload">Select image to upload:</label></td>
                  <td>
                    <input class="form-control" type="file" name="file-to-upload" id="file-to-upload">
                  </td>
                </tr>

              </table>

              <div class="text-center">
                <input class="btn btn-primary" type="submit" value="Upload and Compress Image" name="submit">
              </div>

            </form>
          <?php endif; ?>
        </div>


      </div>
    </div>
  </div>

  <script>
    //$(function() {
    //  $('#form').on('submit', function() {
    //    $('#submit').attr('disabled', 'disabled');
    //  });
    //});
  </script>

</body>
</html>