<?php
require_once __DIR__."/../inc/init.php";

$useTestImages = false; // Set this to true for testing mode
$debug = false;


if ($useTestImages) {

    $batchSize = isset($_POST['limit']) ? (int)$_POST['limit'] : 500;
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

    /* *********************************************************************
        Build SQL from filters
       ********************************************************************* */

    // Get filters
    $selectedBodyParts = get_param("body-parts");
    $selectedAges = get_param("age");

    // Validate and default to "all selected" if nothing was filtered
    $selectedBodyParts = is_array($selectedBodyParts) ? $selectedBodyParts : null;
    $selectedAges = is_array($selectedAges) ? $selectedAges : null;

    if ($debug) {
        zdebug("body-parts:");
        zdebug($selectedBodyParts);
        zdebug("age:");
        zdebug($selectedAges);
    }

    $allBodyParts = ['breast', 'buttocks', 'penis', 'vulva'];

    // If no body-parts selected, but age is selected → default to all body parts
    if (empty($selectedBodyParts) && !empty($selectedAges)) {
        $selectedBodyParts = $allBodyParts;
    }

    $noFiltersSelected = empty($selectedBodyParts) && empty($selectedAges);


    if ($noFiltersSelected) {
        $sql = "
        SELECT `entry_id`, `breast_thumb` AS `filename`, 'breast' AS `type`
        FROM `sou_form_images`
        WHERE `breast_thumb` IS NOT NULL

        UNION ALL

        SELECT `entry_id`, `genital_thumb` AS `filename`, 'genitals' AS `type`
        FROM `sou_form_images`
        WHERE `genital_thumb` IS NOT NULL

        UNION ALL

        SELECT `entry_id`, `buttocks_thumb` AS `filename`, 'buttocks' AS `type`
        FROM `sou_form_images`
        WHERE `buttocks_thumb` IS NOT NULL

        ORDER BY `entry_id` ASC
    ";
    } else {
        // default back to empty array if only one of the filters was missing
        $selectedBodyParts = is_array($selectedBodyParts) ? $selectedBodyParts : [];
        $selectedAges = is_array($selectedAges) ? $selectedAges : [];

        // age filter
        $ageWhere = '1';
        if (!empty($selectedAges)) {
            $ageConditions = [];
            foreach ($selectedAges as $range) {
                if ($range === '70') {
                    $ageConditions[] = "`e`.`age` >= 70";
                } else {
                    [$min, $max] = explode('-', $range);
                    $ageConditions[] = "(`e`.`age` BETWEEN $min AND $max)";
                }
            }
            $ageWhere = implode(' OR ', $ageConditions);
        }

        // subqueries
        $queries = [];

        if (in_array('breast', $selectedBodyParts)) {
            $queries[] = "
            SELECT i.entry_id, i.breast_thumb AS filename, 'breast' AS type
            FROM sou_form_images i
            JOIN sou_form_entries e ON i.entry_id = e.id
            WHERE i.breast_thumb IS NOT NULL
              AND ($ageWhere)
        ";
        }

        if (in_array('buttocks', $selectedBodyParts)) {
            $queries[] = "
            SELECT i.entry_id, i.buttocks_thumb AS filename, 'buttocks' AS type
            FROM sou_form_images i
            JOIN sou_form_entries e ON i.entry_id = e.id
            WHERE i.buttocks_thumb IS NOT NULL
              AND ($ageWhere)
        ";
        }

        if (in_array('penis', $selectedBodyParts)) {
            $queries[] = "
            SELECT i.entry_id, i.genital_thumb AS filename, 'genitals' AS type
            FROM sou_form_images i
            JOIN sou_form_entries e ON i.entry_id = e.id
            JOIN sou_form_anatomy a ON a.entry_id = i.entry_id
            WHERE i.genital_thumb IS NOT NULL
              AND a.value = 'anatomy-male-penis'
              AND ($ageWhere)
        ";
        }

        if (in_array('vulva', $selectedBodyParts)) {
            $queries[] = "
            SELECT i.entry_id, i.genital_thumb AS filename, 'genitals' AS type
            FROM sou_form_images i
            JOIN sou_form_entries e ON i.entry_id = e.id
            JOIN sou_form_anatomy a ON a.entry_id = i.entry_id
            WHERE i.genital_thumb IS NOT NULL
              AND a.value = 'anatomy-female-vulva'
              AND ($ageWhere)
        ";
        }

        // combine
        $sql = !empty($queries) ? implode(" UNION ALL ", $queries) . " ORDER BY entry_id ASC" : "-- No valid filters selected";
    }

// Debug output
    if ($debug) echo "<pre>" . htmlspecialchars($sql) . "</pre>";


    /* *********************************************************************
        Get images and return to frontend
       ********************************************************************* */


    $defaultBatchSize = $isMobile ? 25 : 50; // smaller batch for mobile
    $batchSize = isset($_POST['limit']) ? (int)$_POST['limit'] : $defaultBatchSize;

    try {
     /*   $sql = "SELECT `entry_id`, `breast_thumb` AS `filename`, 'breast' AS `type`
                FROM `sou_form_images`
                WHERE `breast_thumb` IS NOT NULL
            
                UNION ALL
            
                SELECT `entry_id`, `genital_thumb` AS `filename`, 'genitals' AS `type`
                FROM `sou_form_images`
                WHERE `genital_thumb` IS NOT NULL
            
                UNION ALL
            
                SELECT `entry_id`, `buttocks_thumb` AS `filename`, 'buttocks' AS `type`
                FROM `sou_form_images`
                WHERE `buttocks_thumb` IS NOT NULL
            
                ORDER BY `entry_id` ASC";*/

        $stmt = $pdo->query($sql);
        $imageRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $numAvailable = count($imageRows);

        if ($numAvailable === 0) {
            throw new Exception("No image filenames found in database.");
        }

        $images = [];
        for ($i = 0; $i < $batchSize; $i++) {
            $row = $imageRows[random_int(0, $numAvailable - 1)];
            $filename = $row['filename']; // This is the actual filename like SahbYFA8dkw9DhECK1S6-1.jpg
            $url = "https://shapeofus.eu/files/{$filename}";

            $id = pathinfo($filename, PATHINFO_FILENAME); // e.g., SahbYFA8dkw9DhECK1S6-1t
            $id = preg_replace('/-\d+t?$/', '', $id);
            //$id = 1234;
            $id = pathinfo($filename, PATHINFO_FILENAME);

            $images[] = [
                'id' => $id,
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
header('Cache-Control: no-cache, no-store, must-revalidate');
echo json_encode($images, JSON_THROW_ON_ERROR);
