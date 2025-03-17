<?php

$allowedRequestMethods = ['OPTIONS', 'POST'];
if (in_array($_SERVER['REQUEST_METHOD'], $allowedRequestMethods, true)) {
    if (!empty($_POST)) {
        file_put_contents(__DIR__ . '/post.log', 'POST: ' . var_export($_POST, true) . PHP_EOL, FILE_APPEND);
    }
    // Log the request body
    $requestBody = file_get_contents('php://input');
    file_put_contents(__DIR__ . '/post.log', 'BODY: ' . $requestBody . PHP_EOL, FILE_APPEND);
}


$batchSize = isset($_POST['count']) ? (int)$_POST['count'] : 500;
$totalImages = 5000; // Fixed number of images
$images = [];
for ($i = 0; $i < $batchSize; $i++) {
    $imageNumber = random_int(1, $totalImages); // Pick a random image from 1 to 5000
    $images[] = "https://shapeofus.eu/assets/img/testimg/{$imageNumber}.jpg"; // Serve images from your folder
}

http_response_code(200);
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Origin: https://shapeofus.eu');
echo json_encode($images, JSON_THROW_ON_ERROR);