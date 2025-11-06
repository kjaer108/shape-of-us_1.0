<?php
// Returns a JSON array of full-size image URLs for the presentation rotator
// Format: [{"id": <entry_id>, "url": "https://shapeofus.eu/files/<filename>"}, ...]

declare(strict_types=1);

header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

try {
    // Bring in DB connection ($pdo)
    require_once __DIR__ . '/inc/init.php';

    // Read and sanitize limit
    $limit = 50;
    if (isset($_POST['limit'])) {
        $limit = (int)$_POST['limit'];
    } elseif (isset($_GET['limit'])) {
        $limit = (int)$_GET['limit'];
    }
    if ($limit <= 0) { $limit = 50; }
    if ($limit > 200) { $limit = 200; }

    // Build SQL to fetch full-size images (not *_thumb)
    $sql = "
        SELECT *
        FROM (
            SELECT `entry_id`, `breast` AS `filename`, 'breast' AS `type`
            FROM `sou_form_images`
            WHERE `breast` IS NOT NULL AND `breast` <> ''

            UNION ALL

            SELECT `entry_id`, `genital` AS `filename`, 'genitals' AS `type`
            FROM `sou_form_images`
            WHERE `genital` IS NOT NULL AND `genital` <> ''

            UNION ALL

            SELECT `entry_id`, `buttocks` AS `filename`, 'buttocks' AS `type`
            FROM `sou_form_images`
            WHERE `buttocks` IS NOT NULL AND `buttocks` <> ''
        ) AS all_imgs
        ORDER BY RAND()
        LIMIT :limit;
    ";

    // Execute via database.php helper
    $params = [
        ':limit' => ['value' => $limit, 'type' => PDO::PARAM_INT]
    ];
    $rows = pdo_get_array($pdo, $sql, $params);

    $base = 'https://shapeofus.eu/files/';
    $out = [];
    foreach ($rows as $row) {
        $fn = isset($row['filename']) ? (string)$row['filename'] : '';
        if ($fn === '') continue;
        $url = $base . ltrim($fn, '/');
        $out[] = [
            'id' => isset($row['entry_id']) ? (int)$row['entry_id'] : null,
            'url' => $url,
        ];
    }

    echo json_encode($out, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'failed_to_fetch_images',
        'message' => $e->getMessage(),
    ]);
}
