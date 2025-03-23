<?php
require_once __DIR__."/../inc/init.php";

/*
$allowedRequestMethods = ['OPTIONS', 'POST'];
if (in_array($_SERVER['REQUEST_METHOD'], $allowedRequestMethods, true)) {
    if (!empty($_POST)) {
        file_put_contents(__DIR__ . '/get_image.log', 'POST: ' . var_export($_POST, true) . PHP_EOL, FILE_APPEND);
    }
    // Log the request body
    $requestBody = file_get_contents('php://input');
    file_put_contents(__DIR__ . '/get_image.log', 'BODY: ' . $requestBody . PHP_EOL, FILE_APPEND);
}
*/
$image = [
    'id' => $_POST['imageId'] ?? 0,
    'alt' => 'Test image',
    'url' => 'https://shapeofus.eu/files/9288-1.JPG',
    'sections' => [
        [
            'key' => 'general',
            'title' => 'General Information',
            'fields' => [
                [
                    'title' => 'Age',
                    'values' => [
                        [
                            'display_title' => '25',
                        ],
                    ],
                ],
                [
                    'title' => 'Country of Residence',
                    'values' => [
                        [
                            'display_title' => 'Denmark',
                        ]
                    ],
                ],
                [
                    'title' => 'Country of Birth',
                    'values' => [
                        [
                            'display_title' => 'Denmark',
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