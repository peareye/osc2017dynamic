<?php
/**
 * Default Configuration Settings
 *
 * Define all instance specific settings in config.local.php.
 */

/**
 * Production config controls debug and environment mode.
 */
$config['production'] = true;

/**
 * Default Domain
 * Note: Do not include a trailing slash
 */
$config['baseUrl'] = '';

/**
 * Blog Settings
 *
 * Keep it simple for now, just one user is all we need
 */
$config['user']['user_id'] = 1;
$config['user']['email'] = '';
$config['user']['contactEmail'] = '';
$config['user']['htmlTitle'] = '';
$config['user']['htmlAdminTitle'] = '';
$config['user']['blogTitle'] = '';
$config['user']['blogSubTitle'] = '';
$config['user']['defaultMetaDescription'] = '';
$config['user']['sidebarAbout'] = '';
// $config['user']['sidebarRelatedLinks'][] = ['name' = 'Twitter', 'url' = 'https://twitter.com'];

/**
 * Routes
 */
$config['route']['adminSegment'] = 'admin';
$config['route']['wordPressCategories'] = [];

/**
 * Database Settings
 */
$config['database']['host'] = 'localhost';
$config['database']['dbname'] = '';
$config['database']['username'] = '';
$config['database']['password'] = '';

/**
 * Sessions
 */
$config['session']['cookieName'] = ''; // Name of the cookie
$config['session']['checkIpAddress'] = true;
$config['session']['checkUserAgent'] = true;
$config['session']['salt'] = ''; // Salt key to hash
$config['session']['secondsUntilExpiration'] = 7200;

/**
 * File Uploads Config
 *
 * Including trailing slash
 */
$config['file']['filePath'] = ROOT_DIR . 'public/media/large/';
$config['file']['fileThumbPath'] = ROOT_DIR . 'public/media/small/';
$config['file']['fileUri'] = 'media/large/';
$config['file']['fileThumbUri'] = 'media/small/';

/**
 * Review Pagination Options
 */
$config['reviewPagination']['rowsPerPage'] = 3;
$config['reviewPagination']['numberOfLinks'] = 2;

/**
 * Pagination Options
 */
$config['pagination']['rowsPerPage'] = 10;
$config['pagination']['numberOfLinks'] = 2;
