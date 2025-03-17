<?php


if (is_localhost) {
    $envFilePath = "C:\Users\ThomasKjær\PhpstormProjects\zandora.env";
} else {
    $envFilePath = "/var/www/zandora.env";
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
        'username' => getenv('DB_ZAN_USER'),
        'password' => getenv('DB_ZAN_PASS'),
        'charset' => 'utf8mb4'
    ]
];
