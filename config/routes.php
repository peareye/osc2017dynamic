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

    // Preview unpublished post (Used in admin dashboard, the Edit Post Preview uses the savePost route)
    $this->get('/previewpost/{url}', function ($request, $response, $args) {
        return (new Blog\Controllers\AdminController($this))->previewPost($request, $response, $args);
    })->setName('previewPost');

    // Add or edit review
    $this->get('/editreview[/{id}]', function ($request, $response, $args) {
        return (new Blog\Controllers\AdminController($this))->editReview($request, $response, $args);
    })->setName('editReview');

    // Save review
    $this->post('/savereview', function ($request, $response, $args) {
        return (new Blog\Controllers\AdminController($this))->saveReview($request, $response, $args);
    })->setName('saveReview');

    // Delete review
    $this->get('/deletereview/{id}', function ($request, $response, $args) {
        return (new Blog\Controllers\AdminController($this))->deleteReview($request, $response, $args);
    })->setName('deleteReview');

    // View all reviews
    $this->get('/showreviews', function ($request, $response, $args) {
        return (new Blog\Controllers\AdminController($this))->showReviews($request, $response, $args);
    })->setName('showReviews');

    // Load files into gallery (Ajax)
    $this->get('/loadfiles', function ($request, $response, $args) {
        return (new Blog\Controllers\FileController($this))->loadFiles($request, $response, $args);
    })->setName('loadImages');

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

//
// Pages
//

// Gallery
$app->get('/gallery', function ($request, $response, $args) {
    return $this->view->render($response, 'gallery.html');
})->setName('gallery');

// Calendar
$app->get('/calendar', function ($request, $response, $args) {
    return $this->view->render($response, 'calendar.html');
})->setName('calendar');

// Reviews
$app->get('/reviews', function ($request, $response, $args) {
    return $this->view->render($response, 'reviews.html');
})->setName('reviews');

// Rates and Policies
$app->get('/rates', function ($request, $response, $args) {
    (new Blog\Controllers\IndexController($this))->rates($request, $response, $args);
})->setName('rates');

// Contact form
$app->get('/contact', function ($request, $response, $args) {
    return $this->view->render($response, 'contact.html');
})->setName('contact');

// Contact form submit
$app->post('/contactsubmit', function ($request, $response, $args) {
    (new Blog\Controllers\ContactController($this))->sendContactEmail($request, $response, $args);

    return $this->view->render($response, '_thankYou.html');
})->setName('contactSubmit');

// Reservation form
$app->get('/reserve', function ($request, $response, $args) {
    return $this->view->render($response, 'reserve.html');
})->setName('reserve');

// Home page (last route, the default)
$app->get('/', function ($request, $response, $args) {
    (new Blog\Controllers\IndexController($this))->home($request, $response, $args);
})->setName('home');
