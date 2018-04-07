<?php
/**
 * Load Base Files and Return Application
 *
 * Set:
 * - Constants
 * - Composer autoloader
 * - Configuration
 * - Load App
 * - Dependencies
 * - Middleware
 * - Routes
 */

// Define the application root directory
define('ROOT_DIR', dirname(__DIR__) . '/');

// Load the Composer Autoloader
require ROOT_DIR . 'vendor/autoload.php';

// Wrap bootstrap code in an anonymous function to avoid globals
return call_user_func(
    function () {

        // Load default and local configuration settings
        require ROOT_DIR . 'config/config.default.php';
        require ROOT_DIR . 'config/config.local.php';

        // Set error reporting level
        if ($config['production'] === true) {
            // Production
            ini_set('display_errors', 'Off');
            error_reporting(0);
            $config['displayErrorDetails'] = false;
        } else {
            // Development
            error_reporting(-1);
            $config['displayErrorDetails'] = true;
        }

        // Create the application
        $app = new Slim\App(['settings' => $config]);

        // Set up dependencies
        require ROOT_DIR . 'config/dependencies.php';

        // Load routes
        require ROOT_DIR . 'config/routes.php';

        return $app;
    }
);
