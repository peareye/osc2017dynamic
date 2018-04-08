<?php
/**
 * Application Routes
 */

//
// Private routes
//

$app->group("/{$app->getContainer()->get('settings')['route']['adminSegment']}", function () {

    // Main dashboard
    $this->get('/dashboard', function ($request, $response, $args) {
        return (new App\Controllers\AdminController($this))->dashboard($request, $response, $args);
    })->setName('adminDashboard');

    // Add or edit post
    $this->get('/editpost[/{id}]', function ($request, $response, $args) {
        return (new App\Controllers\AdminController($this))->editPost($request, $response, $args);
    })->setName('editPost');

    // Save post
    $this->post('/savepost', function ($request, $response, $args) {
        return (new App\Controllers\AdminController($this))->savePost($request, $response, $args);
    })->setName('savePost');

    // Delete post
    $this->get('/deletepost/{id}', function ($request, $response, $args) {
        return (new App\Controllers\AdminController($this))->deletePost($request, $response, $args);
    })->setName('deletePost');

    // Add or edit review
    $this->get('/editreview[/{id}]', function ($request, $response, $args) {
        return (new App\Controllers\AdminController($this))->editReview($request, $response, $args);
    })->setName('editReview');

    // Save review
    $this->post('/savereview', function ($request, $response, $args) {
        return (new App\Controllers\AdminController($this))->saveReview($request, $response, $args);
    })->setName('saveReview');

    // Approve review
    $this->get('/approvereview/{id}', function ($request, $response, $args) {
        return (new App\Controllers\AdminController($this))->approveReview($request, $response, $args);
    })->setName('approveReview');

    // Delete review
    $this->get('/deletereview/{id}', function ($request, $response, $args) {
        return (new App\Controllers\AdminController($this))->deleteReview($request, $response, $args);
    })->setName('deleteReview');

    // View all reviews
    $this->get('/showreviews', function ($request, $response, $args) {
        return (new App\Controllers\AdminController($this))->showReviews($request, $response, $args);
    })->setName('showReviews');

    // View mail messages
    $this->get('/showmessages', function ($request, $response, $args) {
        return (new App\Controllers\AdminController($this))->showMessages($request, $response, $args);
    })->setName('showMessages');

    // Delete mail message
    $this->get('/deletemessage/{id}', function ($request, $response, $args) {
        return (new App\Controllers\AdminController($this))->deleteMessage($request, $response, $args);
    })->setName('deleteMessage');

    // Help Page
    $this->get('/help', function ($request, $response, $args) {
        return (new App\Controllers\AdminController($this))->help($request, $response, $args);
    })->setName('help');

})->add(function ($request, $response, $next) {
    // Authentication
    $security = $this->securityHandler;

    if (!$security->authenticated()) {
        // Failed authentication, redirect away
        $response = $next($request, $response);
        return $response->withRedirect($this->router->pathFor('login'));
    }

    // Next call
    $response = $next($request, $response);

    return $response;
})->add(function ($request, $response, $next) {
    // Add header to prevent back button access to admin
    $newResponse = $response->withAddedHeader("Cache-Control", "private, no-cache, no-store, must-revalidate");

    // Next call
    $response = $next($request, $newResponse);

    return $response;
});

//
// Public routes
//

// Login - submit request for token
$app->get('/letmein', function ($request, $response, $args) {
    return (new App\Controllers\LoginController($this))->login($request, $response, $args);
})->setName('login');

// Send login token
$app->post('/sendlogintoken/', function ($request, $response, $args) {
    return (new App\Controllers\LoginController($this))->sendLoginToken($request, $response, $args);
})->setName('sendLoginToken');

// Accept login token and set session
$app->get('/logintoken/{token:[a-zA-Z0-9]{64}}', function ($request, $response, $args) {
    return (new App\Controllers\LoginController($this))->processLoginToken($request, $response, $args);
})->setName('processLoginToken');

// Logout
$app->get('/logout', function ($request, $response, $args) {
    return (new App\Controllers\LoginController($this))->logout($request, $response, $args);
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
    return (new App\Controllers\IndexController($this))->showReviews($request, $response, $args);
})->setName('reviews');

// Link to guest review
$app->get('/guestreview', function ($request, $response, $args){
    return (new App\Controllers\IndexController($this))->guestReviewForm($request, $response, $args);
})->setName('guestReview');

// Submit guest review
$app->post('/submitguestreview', function ($request, $response, $args){
    return (new App\Controllers\IndexController($this))->submitGuestReview($request, $response, $args);
})->setName('saveGuestReview');

// Rates and Policies
$app->get('/rates', function ($request, $response, $args) {
    return (new App\Controllers\IndexController($this))->rates($request, $response, $args);
})->setName('rates');

// Contact form
$app->get('/contact', function ($request, $response, $args) {
    return $this->view->render($response, 'contact.html');
})->setName('contact');

// Contact form submit
$app->post('/contactsubmit', function ($request, $response, $args) {
    return (new App\Controllers\ContactController($this))->sendContactEmail($request, $response, $args);
})->setName('contactSubmit');

// Reservation form
$app->get('/reserve', function ($request, $response, $args) {
    return $this->view->render($response, 'reserve.html');
})->setName('reserve');

// Reservtion submit
$app->post('/reservesubmit', function ($request, $response, $args) {
    return (new App\Controllers\ContactController($this))->sendReservationEmail($request, $response, $args);
})->setName('reserveSubmit');

// Thank you page
$app->get('/thankyou/{type}', function ($request, $response, $args) {
    return $this->view->render($response, '_thankYou.html', ['thankYouType' => $args['type']]);
})->setName('thankYou');

// Home page (last route, the default)
$app->get('/', function ($request, $response, $args) {
    return (new App\Controllers\IndexController($this))->home($request, $response, $args);
})->setName('home');
