
<?php
// Ensure page context is available for DeepL before init.php constructs the translator
$page = $page ?? null;
if ($page === null) {
    $pageName = $_REQUEST['page-name'] ?? $_REQUEST['page'] ?? null;
    if ($pageName !== null) {
        $page = ['name' => $pageName];
    }
}

require_once __DIR__."/../inc/init.php";

$imageId = get_param("imageId");

$imageType = explode("-", $imageId)[1];
$imageId = explode("-", $imageId)[0];

//zdebug($imageType);
//zdebug($imageId);

switch ($imageType) {

    case "1t":
        $dbCol = "breast";
        break;

    case "2t":
        $dbCol = "genital";
        break;

    case "3t":
        $dbCol = "buttocks";
        break;

}

$sql = "SELECT sfi.*
        FROM `sou_form_images` sfi
        JOIN `sou_form_entries` sfe ON sfi.`entry_id` = sfe.`id`
        WHERE sfe.`image_token` = :token";
$params = [':token' => $imageId];
$images = pdo_get_row($pdo, $sql, $params);
//zdebug($images[$dbCol]);

$sql = "SELECT * FROM `sou_form_entries` WHERE `image_token` LIKE :token";
$params = [':token' => $imageId];
$imageData = pdo_get_row($pdo, $sql, $params);

$url = "https://shapeofus.eu/files/{$images[$dbCol]}";
//zdebug($url);


function addValue(array &$additionalInfo, string $title, $values, string $note = ''): void {
    $field = [
        'title' => __($title),
        'values' => []
    ];

    if (!empty($note)) {
        $field['note'] = $note;
    }

    // Normalize $values to an array if it's a string
    if (is_string($values) || is_numeric($values)) {
        $values = [$values];
    }

    foreach ($values as $value) {
        $field['values'][] = [
            'display_title' => $value
        ];
    }

    $additionalInfo['fields'][] = $field;
}


/* *************************************************************************
    General Information
   ************************************************************************* */

$generalInfo = [
    'key' => 'general',
    'title' => 'General Information',
    'fields' => []
];

// *** Age ********************************************************************
addValue($generalInfo, 'Age', $imageData['age']);

// *** Country of Residence ***************************************************
addValue($generalInfo, 'Country of Residence', $country_list[$imageData["residence"]]);

// *** Country of Birth *******************************************************
addValue($generalInfo, 'Country of Birth', $country_list[$imageData["birth"]]);

// *** Current Anatomy ********************************************************
$anatomyLabels = [];

if (!empty($imageData["anatomy"])) {
    // Handle encoded JSON array input
    if (is_string($imageData["anatomy"])) {
        $decoded = json_decode(html_entity_decode($imageData["anatomy"]), true);
        $imageData["anatomy"] = is_array($decoded) ? $decoded : [];
    }

    // Collect anatomy labels
    if (is_array($imageData["anatomy"])) {
        foreach ($imageData["anatomy"] as $anatomyItem) {
            $key = trim((string) $anatomyItem);
            $anatomyLabels[] = $current_anatomy[$key]["label"] ?? $key;
        }
    }
}

// Only add the field if we have something
if (!empty($anatomyLabels)) {
    addValue($generalInfo, 'Current Anatomy', $anatomyLabels);
}



/* *************************************************************************
    Additional Information
   ************************************************************************* */

$additionalInfo = [
    'key' => 'additional',
    'title' => 'Additional Information',
    'fields' => []
];

//zdebug($additionalInfo);

// *** Presence of Hair on Body ***********************************************

if (!empty($imageData["hair_chest"]) && $imageType == "1t") {
    addValue($additionalInfo, 'Hair on Chest/Breast', $additional_information[$imageData["hair_chest"]]["label"]);
}

if (!empty($imageData["hair_above"]) && $imageType == "2t") {
    addValue($additionalInfo, 'Hair on Upper Genital Area', $additional_information[$imageData["hair_above"]]["label"]);
}

if (!empty($imageData["hair_below"]) && $imageType == "2t") {
    addValue($additionalInfo, 'Hair on Lower Genital Area', $additional_information[$imageData["hair_below"]]["label"]);
}

if (!empty($imageData["hair_buttocks"]) && $imageType == "3t") {
    addValue($additionalInfo, 'Hair on Buttocks', $additional_information[$imageData["hair_buttocks"]]["label"]);
}


// *** Stretch Marks or Scars *************************************************

if (!empty($imageData["marks"]) && $imageData["marks"] != '["marks-none"]') {
    $decoded = json_decode(html_entity_decode($imageData["marks"]), true);

    if (is_array($decoded)) {
        $labels = [];

        foreach ($decoded as $key) {
            if (!empty($additional_information[$key]["label"])) {
                $labels[] = $additional_information[$key]["label"];
            }
        }

        if (!empty($labels)) {

            if (empty($imageData["marks_text"])) {
                addValue($additionalInfo, 'Stretch Marks or Scars', $labels);
            } else {
                addValue($additionalInfo, 'Stretch Marks or Scars', $labels, $imageData["marks_text"]);
            }
        }
    }
}

// *** Pregnancy and Childbirth related ***************************************

if (!empty($imageData["pregnancy"]) && $imageData["pregnancy"] == 'pregnancy-true') {
    addValue($additionalInfo, 'Pregnancy', $additional_information[$imageData["pregnancy"]]["label"]);
}

if (!empty($imageData["vaginal_birth"]) && $imageType == "2t" && $imageData["vaginal_birth"] == 'vaginal-birth-true') {
    addValue($additionalInfo, 'Vaginal Birth', $additional_information[$imageData["pregnancy"]]["label"]);
}

if (!empty($imageData["c_section"]) && $imageType == "2t" && $imageData["c_section"] == 'c-section-true') {
    addValue($additionalInfo, 'Cesarean birth (C-section)', $additional_information[$imageData["c_section"]]["label"]);
}

if (!empty($imageData["breastfeeding"]) && $imageType == "1t" && $imageData["breastfeeding"] != 'breastfeeding-false') {
    addValue($additionalInfo, 'Breastfeeding', $additional_information[$imageData["breastfeeding"]]["label"]);
}

// *** Piercings **************************************************************

if (!empty($imageData["piercings"]) && $imageData["piercings"] != '["piercings-false"]') {
    $decoded = json_decode(html_entity_decode($imageData["piercings"]), true);

    if (is_array($decoded)) {
        $labels = [];

        foreach ($decoded as $key) {
            if (!empty($additional_information[$key]["label"])) {
                $labels[] = $additional_information[$key]["label"];
            }
        }

        if (!empty($labels)) {

            if (empty($imageData["piercings_other_text"])) {
                addValue($additionalInfo, 'Piercings', $labels);
            } else {
                addValue($additionalInfo, 'Piercings', $labels, $imageData["piercings_other_text"]);
            }
        }
    }
}

// *** Tattoos ****************************************************************

if (!empty($imageData["tattoos"]) && $imageData["tattoos"] != '["tattoos-false"]') {
    $decoded = json_decode(html_entity_decode($imageData["tattoos"]), true);

    if (is_array($decoded)) {
        $labels = [];

        foreach ($decoded as $key) {
            if (!empty($additional_information[$key]["label"])) {
                $labels[] = $additional_information[$key]["label"];
            }
        }

        if (!empty($labels)) {

            if (empty($imageData["tattoos_other_text"])) {
                addValue($additionalInfo, 'Tattoos', $labels);
            } else {
                addValue($additionalInfo, 'Tattoos', $labels, $imageData["tattoos_other_text"]);
            }
        }
    }
}


// *** Hormonal Treatment *****************************************************

if (!empty($imageData["hormonal_influence"]) && $imageData["hormonal_influence"] == 'hormonal-influence-true') {
    addValue($additionalInfo, 'Hormonal Treatment', $additional_information[$imageData["hormonal_influence"]]["label"]);
}

// *** Menstrual Cycle ********************************************************

if (!empty($imageData["menstrual_cycle"]) && $imageData["menstrual_cycle"] == 'menstrual-cycle-true') {
    addValue($additionalInfo, 'Menstrual Cycle', $additional_information[$imageData["menstrual_cycle"]]["label"]);
}



/* *************************************************************************
    Build Form Array
   ************************************************************************* */

$infoArray = [
    'id' => $_POST['imageId'] ?? 0,
    'alt' => 'Test image',
    'url' => $url,
    'sections' => [
        $generalInfo,
//        [
//            'key' => 'medical',
//            'title' => 'Surgical or Medical History',
//            'fields' => [
//                [
//                    'title' => 'Hormone Therapy',
//                    'note' => 'Used by transgender men (FtM) and some intersex individuals to develop muscle mass, facial hair, and alter fat distribution.',
//                    'values' => [
//                        [
//                            'display_title' => 'Puberty Blockers',
//                        ],
//                        [
//                            'display_title' => 'Hormone Blockers',
//                        ],
//                        [
//                            'display_title' => 'Testosterone Therapy',
//                        ],
//                    ],
//                ],
//            ],
//        ],
//        (!empty($additionalInfo['fields'])) ? $additionalInfo : ''
    ],
];

if (!empty($additionalInfo['fields'])) {
    $infoArray['sections'][] = $additionalInfo;
}

http_response_code(200);
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Origin: http://localhost:3000');
echo json_encode($infoArray, JSON_THROW_ON_ERROR);