<?php
require_once __DIR__."/../inc/init.php";

$useTestImages = true; // Set this to true for testing mode

if ($useTestImages) {

    $batchSize = isset($_POST['count']) ? (int)$_POST['count'] : 500;
    $totalImages = 5000; // Fixed number of images
    $images = [];

    for ($i = 0; $i < $batchSize; $i++) {
        $imageNumber = random_int(1, $totalImages);
        $filename = "{$imageNumber}.jpg";
        $url = "https://shapeofus.eu/assets/img/testimg/{$filename}";
        $images[] = [
            'id' => (string)$imageNumber,
            'url' => $url
        ];
    }

} else {

    $batchSize = isset($_POST['count']) ? (int)$_POST['count'] : 500;

    try {
        // Fetch all photo_ids
        $stmt = $pdo->query("SELECT `photo_id` FROM `sou_form_entries` WHERE `photo_id` IS NOT NULL");
        $photoIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $images = [];
        $numAvailable = count($photoIds);

        if ($numAvailable === 0) {
            throw new Exception("No photo_ids found in database.");
        }

        for ($i = 0; $i < $batchSize; $i++) {
            $photoId = $photoIds[random_int(0, $numAvailable - 1)];
            $version = random_int(1, 3); // Choose version 1-3
            $filename = "{$photoId}-{$version}.JPG";
            $url = "https://shapeofus.eu/files/{$filename}";
            $images[] = [
                'id' => "{$photoId}-{$version}",
                'url' => $url
            ];
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Image generation failed', 'details' => $e->getMessage()]);
        exit;
    }
}

// Send response
http_response_code(200);
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Origin: https://shapeofus.eu');
echo json_encode($images, JSON_THROW_ON_ERROR);
