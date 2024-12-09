<?php

// Load environment variables from the .env file
require_once __DIR__ . '/../utils/env-loader.php';
loadEnv(__DIR__ . '/../.env');

$GLOBALS['CONFIG'] = [
    'CLIENT_ID' => $_ENV['CLIENT_ID'],
    'TOKEN_B2B' => $_ENV['TOKEN_B2B'],
    'CHANNEL_ID' => $_ENV['CHANNEL_ID'],
    'BASE_URL' => $_ENV['BASE_URL'],
    'CLIENT_SECRET' => $_ENV['CLIENT_SECRET'] ,
    'YOUR_URL' => $_ENV['YOUR_URL']
];