<?php
/**
 * Index Controller
 */
namespace App\Controllers;

class IndexController extends BaseController
{
    /**
     * Get Home Page
     */
    public function home($request, $response, $args)
    {
        // Get dependencies
        $postMapper = $this->container->get('postMapper');
        $reviewMapper = $this->container->get('reviewMapper');

        $page['welcome'] = $postMapper->getSinglePost(1);
        $page['house'] = $postMapper->getSinglePost(2);
        $page['provide'] = $postMapper->getSinglePost(3);
        $page['comfort'] = $postMapper->getSinglePost(4);

        // Render view
        $this->container->view->render($response, 'home.html', $page);
    }

    /**
     * Get Rates and Policies Page
     */
    public function rates($request, $response, $args)
    {
        // Get dependencies
        $postMapper = $this->container->get('postMapper');

        $page['summer'] = $postMapper->getSinglePost(6);
        $page['winter'] = $postMapper->getSinglePost(7);
        $page['details'] = $postMapper->getSinglePost(8);
        $page['policies'] = $postMapper->getSinglePost(9);

        // Render view
        $this->container->view->render($response, 'rates.html', $page);
    }

    /**
     * Search Posts and Pages
     */
    public function search($request, $response, $args)
    {
        // Get dependencies
        $postMapper = $this->container->get('postMapper');
        $pagination = $this->container->get('pagination');

        // If no search term was provided, go home
        if (!$terms = $this->container->request->getParam('terms')) {
            // Redirect home
            return $response->withRedirect($this->container->router->pathFor('home'));
        }

        // Get the page number and setup pagination
        $pageNumber = ($this->container->request->getParam('page')) ?: 1;
        $pagination->setPagePath($this->container->router->pathFor('search'), ['terms' => $terms]);
        $pagination->setCurrentPageNumber($pageNumber);

        // Fetch posts with limit and offset
        $posts = $postMapper->search($terms, $pagination->getRowsPerPage(), $pagination->getOffset(), true, false);

        // Get total row count and add extension
        $pagination->setTotalRowsFound($postMapper->foundRows());
        $this->container->view->addExtension($pagination);

        // Render view
        return $this->container->view->render($response, 'home.html', ['posts' => $posts, 'search' => $terms]);
    }

   /**
     * Show Reviews
     */
    public function showReviews($request, $response, $args)
    {
        // Get dependencies
        $reviewMapper = $this->container['reviewMapper'];
        $pagination = $this->container->get('reviewPagination');

        // Get the page number and setup pagination
        $pageNumber = ($this->container->request->getParam('page')) ?: 1;
        $pagination->setPagePath($this->container->router->pathFor('reviews'));
        $pagination->setCurrentPageNumber($pageNumber);

        // Fetch approved reviews with limit and offset
        $reviews = $reviewMapper->getApprovedReviews($pagination->getRowsPerPage(), $pagination->getOffset());

        // Get total row count from query and add extension
        $pagination->setTotalRowsFound($reviewMapper->foundRows());
        $this->container->view->addExtension($pagination);

        // Check if this an Ajax request and return appropriate pagelet
        if ($request->isXhr()) {
            return $this->container->view->render($response, '_reviewSet.html', ['reviews' => $reviews]);
        }

        // Otherwise return normal reviews page
        return $this->container->view->render($response, 'reviews.html', ['reviews' => $reviews]);
    }

    /**
     * Submit Guest Review
     *
     * Guests may submit a review in response to a request
     */
    public function guestReviewForm($request, $response, $args)
    {
        $reviewMapper = $this->container->reviewMapper;

        // Get parameters
        $reviewRequestId = $request->getQueryParam('id');
        $reviewRequestToken = $request->getQueryParam('token');
        $review = $reviewMapper->findById($reviewRequestId);

        // Verify we have a valid review request
        if (is_int($review->id) && $review->token === $reviewRequestToken) {
            return $this->container->view->render($response, 'submitGuestReview.html', ['review' => $review]);
        }

        // Otherwise do nothing
        return $response->withRedirect($this->container->router->pathFor('home'));
    }

    /**
     * Save Guest Review
     */
    public function submitGuestReview($request, $response, $args)
    {
        // Get dependencies
        $reviewMapper = $this->container['reviewMapper'];
        $markdown = $this->container->get('markdownParser');
        $message = $this->container->mailMessage;
        $mailer = $this->container->sendMailMessage;
        $config = $this->container->settings;

        // Get review object
        $review = $reviewMapper->findById($request->getParsedBodyParam('id'));

        // Save, but first verify token
        if (is_int($review->id) && $review->token === $request->getParsedBodyParam('token')) {
            $review->id = $request->getParsedBodyParam('id');
            $review->title = $request->getParsedBodyParam('title');
            $review->content = $request->getParsedBodyParam('content');
            $review->content_html = $markdown->text($request->getParsedBodyParam('content'));
            $review->who = $request->getParsedBodyParam('who');
            $review->review_date = $request->getParsedBodyParam('review_date');
            $review->approved = ($request->getParsedBodyParam('approved')) ? 'Y' : 'N';
            $review->rating = $request->getParsedBodyParam('rating');
            $review->email = $request->getParsedBodyParam('email');
            $review->token = '';

            $review = $reviewMapper->save($review);

            // Add link to message body
            $host = $request->getUri()->getHost();
            $messageBody = "A guest review was submitted by {$review->email} and is awaiting approval.";

            // Set the "from" address based on host, and strip "www."
            $host = preg_replace('/^www\./i', '', $host);

            // If sending from localhost, add .com to the end to make mail happy
            if ($host === 'localhost') {
                $host .= '.com';
            }

            // Add link to admin login
            $domain = $request->getUri()->getScheme() . '://' . $request->getUri()->getHost();
            $loginPath = $this->container->router->pathFor('showReviews');
            $messageBody .= "\n\n{$domain}{$loginPath}";

            // Construct message
            $message
                ->setFrom("OurSandCastle <send@{$host}>")
                ->addTo($config['user']['contactEmail'])
                ->setSubject('A Guest Review Was Submitted')
                ->setBody($messageBody);

            // Send
            $mailer->send($message);
        }

        // Display dashboard
        return $response->withRedirect($this->container->router->pathFor('thankYou'));
    }
}
