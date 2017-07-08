<?php
// If a thumbnail of the required size is not found, then make one
// This file is invoked by the htaccess file in public/.htaccess when an image file is not found

// Define the root directory
define('ROOT_DIR', dirname(__DIR__) . '/');

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/config.default.php';
require __DIR__ . '/../config/config.local.php';

$config = [
    'showErrors' => true,
    'pathToOriginalImage' => $config['file']['filePath'],
    'pathToThumbImage' => $config['file']['fileThumbPath'],
    'matchRoutePattern' => '/media\/small\/(?<dims>[0-9]*x[0-9]*)\/(?<subdir>..)\/(?<image>.*)$/i',
];

$img = new App\Library\Thumbnailer($config, new Intervention\Image\ImageManager());

$img->make();
