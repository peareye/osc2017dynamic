<?php
/**
 * Application Routes
 */

//
// Private routes
//

$app->group("/{$app->getContainer()->get('settings')['route']['adminSegment']}", function () {

    // Validate unique post URL (Ajax request)
    $this->post('/validateurl', function ($request, $response, $args) {
        return (new Blog\Controllers\AdminController($this))->validateUniqueUrl($request, $response, $args);
    });

    // Main dashboard
    $this->get('/dashboard', function ($request, $response, $args) {
        return (new Blog\Controllers\AdminController($this))->dashboard($request, $response, $args);
    })->setName('adminDashboard');

    // Add or edit post
    $this->get('/editpost[/{id}]', function ($request, $response, $args) {
        return (new Blog\Controllers\AdminController($this))->editPost($request, $response, $args);
    })->setName('editPost');

    // Save post
    $this->post('/savepost', function ($request, $response, $args) {
        return (new Blog\Controllers\AdminController($this))->savePost($request, $response, $args);
    })->setName('savePost');

    // Delete post
    $this->get('/deletepost/{id}', function ($request, $response, $args) {
        return (new Blog\Controllers\AdminController($this))->deletePost($request, $response, $args);
    })->setName('deletePost');

    // Unpublish post
    $this->get('/unpublishpost/{id}', function ($request, $response, $args) {
        return (new Blog\Controllers\AdminController($this))->unpublishPost($request, $response, $args);
    })->setName('unpublishPost');

    // Make sitemap
    $this->get('/updatesitmap', function ($request, $response, $args) {
        return (new Blog\Controllers\AdminController($this))->updateSitemap($request, $response, $args);
    })->setName('updateSitemap');

    // Upload file
    $this->post('/uploadfile', function ($request, $response, $args) {
        return (new Blog\Controllers\FileController($this))->uploadFile($request, $response, $args);
    })->setName('uploadFile');

    // Delete uploaded file
    $this->post('/deletefile', function ($request, $response, $args) {
        return (new Blog\Controllers\FileController($this))->deleteFile($request, $response, $args);
    })->setName('deleteFile');

    // Preview unpublished post (Used in admin dashboard, the Edit Post Preview uses the savePost route)
    $this->get('/previewpost/{url}', function ($request, $response, $args) {
        return (new Blog\Controllers\AdminController($this))->previewPost($request, $response, $args);
    })->setName('previewPost');

    // Load files into gallery (Ajax)
    $this->get('/loadfiles', function ($request, $response, $args) {
        return (new Blog\Controllers\FileController($this))->loadFiles($request, $response, $args);
    })->setName('loadImages');

    // Show comments
    $this->get('/comments', function ($request, $response, $args) {
        return (new Blog\Controllers\CommentController($this))->showAll($request, $response, $args);
    })->setName('comments');

    // Change comment status
    $this->get('/commentstatus/{commentId}/{newStatus}', function ($request, $response, $args) {
        return (new Blog\Controllers\CommentController($this))->changeCommentStatus($request, $response, $args);
    })->setName('changeCommentStatus');

    // Delete comment
    $this->get('/deletecomment/{commentId}', function ($request, $response, $args) {
        return (new Blog\Controllers\CommentController($this))->deleteComment($request, $response, $args);
    })->setName('deleteComment');

    // Search posts and pages
    $this->get('/search', function ($request, $response, $args) {
        return (new Blog\Controllers\AdminController($this))->search($request, $response, $args);
    })->setName('adminSearch');

})->add(function ($request, $response, $next) {
    // Authentication
    $security = $this->get('securityHandler');

    if (!$security->authenticated()) {
        // Failed authentication, redirect away
        $response = $next($request, $response);
        return $response->withRedirect($this->router->pathFor('home'));
    }

    // Next call
    $response = $next($request, $response);

    return $response;
});

//
// Public routes
//

// Login - submit request for token
$app->get('/letmein', function ($request, $response, $args) {
    return (new Blog\Controllers\LoginController($this))->login($request, $response, $args);
})->setName('login');

// Send login token
$app->post('/sendlogintoken/', function ($request, $response, $args) {
    return (new Blog\Controllers\LoginController($this))->sendLoginToken($request, $response, $args);
})->setName('sendLoginToken');

// Accept login token and set session
$app->get('/logintoken/{token:[a-zA-Z0-9]{64}}', function ($request, $response, $args) {
    return (new Blog\Controllers\LoginController($this))->processLoginToken($request, $response, $args);
})->setName('processLoginToken');

// Logout
$app->get('/logout/', function ($request, $response, $args) {
    return (new Blog\Controllers\LoginController($this))->logout($request, $response, $args);
})->setName('logout');

// Contact form
$app->get('/contact[-{me}]', function ($request, $response, $args) {
    return (new Blog\Controllers\IndexController($this))->contact($request, $response, $args);
})->setName('contactForm');

// View page
$app->get('/{url}', function ($request, $response, $args) {
    return (new Blog\Controllers\IndexController($this))->viewPost($request, $response, $args);
})->setName('viewPage');

// Home page (last route, the default)
$app->get('/', function ($request, $response, $args) {
    return (new Blog\Controllers\IndexController($this))->home($request, $response, $args);
})->setName('home');
