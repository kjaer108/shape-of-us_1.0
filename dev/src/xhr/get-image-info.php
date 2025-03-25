<?php
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

$image = [
    'id' => $_POST['imageId'] ?? 0,
    'alt' => 'Test image',
    'url' => $url,
    'sections' => [
        [
            'key' => 'general',
            'title' => 'General Information',
            'fields' => [
                [
                    'title' => 'Age',
                    'values' => [
                        [
                            'display_title' => $imageData["age"],
                        ],
                    ],
                ],
                [
                    'title' => 'Country of Residence',
                    'values' => [
                        [
                            'display_title' => $country_list[$imageData["residence"]],
                        ]
                    ],
                ],
                [
                    'title' => 'Country of Birth',
                    'values' => [
                        [
                            'display_title' => $country_list[$imageData["birth"]],
                        ],
                    ],
                ],
                [
                    'title' => 'Current Anatomy',
                    'values' => [
                        [
                            'display_title' => 'Male to Female (MtF)',
                        ],
                        [
                            'display_title' => 'Post-surgery Transgender Vulva',
                        ],
                    ],
                ],
            ],
        ],
        [
            'key' => 'medical',
            'title' => 'Surgical or Medical History',
            'fields' => [
                [
                    'title' => 'Hormone Therapy',
                    'note' => 'Used by transgender men (FtM) and some intersex individuals to develop muscle mass, facial hair, and alter fat distribution.',
                    'values' => [
                        [
                            'display_title' => 'Puberty Blockers',
                        ],
                        [
                            'display_title' => 'Hormone Blockers',
                        ],
                        [
                            'display_title' => 'Testosterone Therapy',
                        ],
                    ],
                ],
            ],
        ],
        [
            'key' => 'additional',
            'title' => 'Additional Information',
            'fields' => [
                [
                    'title' => 'Stretch Marks or Scars',
                    'note' => 'I got the scar from fighting a bear on a vacation in Canada.',
                    'values' => [
                        [
                            'display_title' => 'Scars',
                        ],
                    ],
                ],
                [
                    'title' => 'Pregnancy',
                    'values' => [
                        [
                            'display_title' => 'Yes',
                        ],
                    ],
                ],
            ],
        ],
    ],
];

http_response_code(200);
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Origin: http://localhost:3000');
echo json_encode($image, JSON_THROW_ON_ERROR);