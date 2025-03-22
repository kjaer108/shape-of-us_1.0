<?php


if (IS_LOCALHOST) {
    $envFilePath = "C:\Users\ThomasKjær\PhpstormProjects\shapeofus.env";
} else {
    $envFilePath = "/var/www/shapeofus.env";
}

if (file_exists($envFilePath) && is_readable($envFilePath)) {
    $envVars = parse_ini_file($envFilePath);

    if ($envVars === false) {
        die("Error: Unable to parse .env file.");
    }

    // Set environment variables dynamically
    foreach ($envVars as $key => $value) {
        putenv("$key=$value");
        $_ENV[$key] = $value;
    }

    // Debugging: Check if variables are set
    //echo "Loaded ENV: " . print_r($envVars, true) . "<br>";
} else {
    die("Error: .env file does not exist or is not readable.");
}


return [
    // Linode MariaDB
    'DB' => [
        'host' => '194.233.171.92',
        'username' => getenv('DB_USER'),
        'password' => getenv('DB_PASS'),
        'charset' => 'utf8mb4'
    ],
    'BREVO' => [
        'api' => [
            'host' => 'https://api.brevo.com',
            'name' => getenv('BREVO_API_NAME'),
            'key' => getenv('BREVO_API_KEY'),
        ],
        'list' => [
            'newsletter' => 7
        ]
    ],
    'deepL' => [
        'apiKey' => getenv('DEEPL_API_KEY'),
        'endpoint' => 'https://api-free.deepl.com/'
    ]
];
