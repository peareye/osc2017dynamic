<?php
// DIC configuration

$container = $app->getContainer();

// Twig templates
$container['view'] = function ($c) {
    // Include theme name in first (default) path, the second path is for admin templates
    $templatePaths = [
        ROOT_DIR . 'templates/' . trim($c->get('settings')['theme'], '/'),
        ROOT_DIR . 'templates/default/',
        'admin' => ROOT_DIR . 'templates/admin',
    ];

    $view = new Slim\Views\Twig($templatePaths, [
        'cache' => ROOT_DIR . 'twigcache',
        'debug' => !$c->get('settings')['production'],
    ]);

    $view->addExtension(new Blog\Extensions\TwigExtension($c));

    if ($c->get('settings')['production'] === false) {
        $view->addExtension(new Twig_Extension_Debug());
    }

    return $view;
};

// Twig pagination extenion
$container['pagination'] = function ($c) {
    return new Blog\Extensions\PaginationExtension($c->get('settings')['pagination']);
};

// Monolog logging
$container['logger'] = function ($c) {
    $level = ($c->get('settings')['production']) ? Monolog\Logger::ERROR : Monolog\Logger::DEBUG;
    $logger = new Monolog\Logger('blog');
    // $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler(ROOT_DIR . 'logs/' . date('Y-m-d') . '.log', $level));

    return $logger;
};

// Database connection
$container['database'] = function ($c) {
    $dbConfig = $c->get('settings')['database'];

    // Extra database options
    $dbConfig['options'][PDO::ATTR_PERSISTENT] = true;
    $dbConfig['options'][PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
    $dbConfig['options'][PDO::ATTR_EMULATE_PREPARES] = false;

    // Define connection string
    $dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']};charset=utf8mb4";

    // Return connection
    return new PDO($dsn, $dbConfig['username'], $dbConfig['password'], $dbConfig['options']);
};

// Custom error handling (overwrite Slim errorHandler to add logging)
$container['errorHandler'] = function ($c) {
    return new \Blog\Extensions\Error($c->get('settings')['displayErrorDetails'], $c['logger']);
};

// Sessions
$container['sessionHandler'] = function ($c) {
    return new WolfMoritz\Session\SessionHandler($c['database'], $c->get('settings')['session']);
};

// Load Toolbox
$container['toolbox'] = function ($c) {
    return new Blog\Library\Toolbox();
};

// Override the default Not Found Handler
$container['notFoundHandler'] = function ($c) {
    return new Blog\Extensions\NotFound($c->get('view'), $c->get('logger'));
};

// Post Data Mapper
$container['postMapper'] = $container->factory(function ($c) {
    return new Blog\Models\PostMapper($c['database'], $c['logger'], ['user_id' => 1]);
});

// Comment Data Mapper
$container['commentMapper'] = $container->factory(function ($c) {
    return new Blog\Models\CommentMapper($c['database'], $c['logger'], ['user_id' => 1]);
});

// Mail message
$container['mailMessage'] = $container->factory(function ($c) {
    return new Nette\Mail\Message;
});

// Send mail message
$container['sendMailMessage'] = function ($c) {
    return new Nette\Mail\SendmailMailer();
};

// Security Handler
$container['securityHandler'] = function ($c) {
    return new Blog\Library\SecurityHandler($c->get('sessionHandler'));
};

// Sitemap
$container['sitemapHandler'] = function ($c) {
    return new Blog\Library\SitemapHandler($c->get('logger'), [
        'sitemapFilePath' => ROOT_DIR . 'public/',
        'baseUrl' => $c->get('settings')['baseUrl'],
        'alertSearchEngines' => $c->get('settings')['production'],
    ]);
};

// Markdown parser
$container['markdownParser'] = function ($c) {
    return new Parsedown();
};

// File uploader
$container['fileUploader'] = function ($c) {
    return new Blog\Library\FileUploader($c['request']->getUploadedFiles(), $c['settings']['file']);
};

// Image manipulation
$container['imageManipulator'] = function ($c) {
    return new Intervention\Image\ImageManager();
};
