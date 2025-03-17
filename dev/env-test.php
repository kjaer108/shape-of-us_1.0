<?php

$envFilePath = "/var/www/zandora.env";
$envFilePath = "C:\Users\ThomasKjær\PhpstormProjects\zandora.env";

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
    echo "Loaded ENV: " . print_r($envVars, true) . "<br>";
} else {
    die("Error: .env file does not exist or is not readable.");
}

// Now you can safely access them
$databaseUser = getenv('DB_ZAN_USER');

if (!$databaseUser) {
    die("Error: ENV variable DB_ZAN_USER is not set.");
}

echo "DB User: $databaseUser";