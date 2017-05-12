<?php

// Set encoding
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

// Load the bootstrap file to get things started
$app = require __DIR__ . '/../app/bootstrap.php';

// And away we go!
$app->run();
