<?php
declare(strict_types=1);

$rootPath = __DIR__ . '/../application';
$appPath = $rootPath . '/app';

require $rootPath . '/vendor/autoload.php';

$dotenv = new \Dotenv\Dotenv($rootPath);
$dotenv->load();

session_start();

// Instantiate the app
$settings = require $appPath . '/settings.php';
$app = new \Slim\App(['settings' => $settings]);

// Set up dependencies
require $appPath . '/dependencies.php';

// Register middlewares
require $appPath . '/middlewares.php';

// Register routes
require $appPath . '/routes.php';

// Run app
$app->run();
