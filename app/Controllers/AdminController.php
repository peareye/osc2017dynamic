<?php
/**
 * Index Controller
 */
namespace App\Controllers;

use \Exception;

class AdminController extends BaseController
{
    /**
     * Post Domain Object
     * @var App\Models\DomainObjectAbstract
     */
    protected $post;

    /**
     * Get Home Page
     */
    public function dashboard($request, $response, $args)
    {
        // Get dependencies
        $postMapper = $this->container['postMapper'];
        $pagination = $this->container->get('pagination');

        // Get the page number and setup pagination
        $pageNumber = ($this->container->request->getParam('page')) ?: 1;
        $pagination->setPagePath($this->container->router->pathFor('adminDashboard'));
        $pagination->setCurrentPageNumber($pageNumber);

        // Fetch posts
        $posts = $postMapper->getPosts($pagination->getRowsPerPage(), $pagination->getOffset(), false, false);

        // Get total row count and add extension
        $pagination->setTotalRowsFound($postMapper->foundRows());
        $this->container->view->addExtension($pagination);

        return $this->container->view->render($response, '@admin/dashboard.html', ['posts' => $posts]);
    }
    /**
     * Get Help Page
     */
    public function help($request, $response, $args)
    {
        // Get dependencies
        //  $postMapper = $this->container['postMapper'];
        //$pagination = $this->container->get('pagination');

        // Get the page number and setup pagination
        // $pageNumber = ($this->container->request->getParam('page')) ?: 1;
        // $pagination->setPagePath($this->container->router->pathFor('adminDashboard'));
        // $pagination->setCurrentPageNumber($pageNumber);

        // Fetch posts
        // $posts = $postMapper->getPosts($pagination->getRowsPerPage(), $pagination->getOffset(), false, false);

        // Get total row count and add extension
        // $pagination->setTotalRowsFound($postMapper->foundRows());
        // $this->container->view->addExtension($pagination);

        return $this->container->view->render($response, '@admin/help.html');
    }

    /**
     * Add/Edit Post
     */
    public function editPost($request, $response, $args)
    {
        // Get dependencies
        $postMapper = $this->container['postMapper'];

        // Was an ID supplied?
        $id = isset($args['id']) ? $args['id'] : null;

        if (null === $post = $postMapper->findById($id)) {
            $post = $postMapper->make();
        }

        // Get available templates and set default if non was selected
        $post->allTemplates = $this->getThemeTemplates();
        $post->template = (!$post->template) ? 'post.html' : $post->template;

        return $this->container->view->render($response, '@admin/editPost.html', ['post' => $post]);
    }

    /**
     * Save Post
     */
    public function savePost($request, $response, $args)
    {
        // Get dependencies
        $postMapper = $this->container['postMapper'];

        // Make blog post object
        $this->post = $postMapper->make();

        // Process data
        $this->processPostData($request, $response, $args);

        // Was this to preview or save post?
        if ($request->getParsedBodyParam('button') === 'preview') {
            // Preview
            return $this->previewPost($request, $response, $args);
        } else {
            // Save
            $this->post = $postMapper->save($this->post);
        }

        // Display admin dashboard
        return $response->withRedirect($this->container->router->pathFor('editPost', ['id' => $this->post->id]));
    }

    /**
     * Delete Blog Post
     */
    public function deletePost($request, $response, $args)
    {
        // Get dependencies
        $postMapper = $this->container['postMapper'];

        $post = $postMapper->make();
        $post->id = (int) $args['id'];

        $postMapper->delete($post);

        return $response->withRedirect($this->container->router->pathFor('adminDashboard'));
    }

    /**
     * Unpublish Blog Post
     */
    public function unpublishPost($request, $response, $args)
    {
        // Get dependencies
        $postMapper = $this->container['postMapper'];

        $post = $postMapper->make();
        $post->id = (int) $args['id'];
        $post->published_date = '';

        $postMapper->save($post);

        return $response->withRedirect($this->container->router->pathFor('adminDashboard'));
    }

    /**
     * Preview Post
     *
     * This is mapped to GET and POST routes
     */
    public function previewPost($request, $response, $args)
    {
        // What kind of request?
        if ($request->isGet()) {
            // Called from the admin dashboard (not savePost Preview)
            // Fetch the requested post
            $postMapper = $this->container['postMapper'];
            $this->post = $postMapper->getSinglePost($args['url'], false);
        }

        // Otherwise this is being called from savePost() and $this->post is set

        // Was anything found (for GET)?
        if (empty($this->post)) {
            return $this->notFound($request, $response);
        }

        $this->container->view->render($response, 'post.html', ['post' => $this->post, 'metaDescription' => $this->post->meta_description]);
    }

    /**
     * Add/Edit Review
     */
    public function editReview($request, $response, $args)
    {
        // Get dependencies
        $reviewMapper = $this->container['reviewMapper'];

        // Was an ID supplied?
        $id = isset($args['id']) ? $args['id'] : null;

        if (null === $review = $reviewMapper->findById($id)) {
            $review = $reviewMapper->make();
        }

        return $this->container->view->render($response, '@admin/editReview.html', ['review' => $review]);
    }

    /**
     * Approve Review
     *
     * Quickly approve review
     */
    public function approveReview($request, $response, $args)
    {
        // Get dependencies
        $reviewMapper = $this->container->reviewMapper;

        if (null !== $review = $reviewMapper->findById($args['id'])) {
            $review->approved = 'Y';
            $review = $reviewMapper->save($review);
        }

        return $response->withRedirect($this->container->router->pathFor('showReviews'));
    }

    /**
     * Save Review
     */
    public function saveReview($request, $response, $args)
    {
        // Get dependencies
        $reviewMapper = $this->container['reviewMapper'];
        $markdown = $this->container->get('markdownParser');
        $message = $this->container->mailMessage;
        $mailer = $this->container->sendMailMessage;
        $config = $this->container->settings;

        // Make review object
        $review = $reviewMapper->make();

        $review->id = $request->getParsedBodyParam('id');
        $review->title = $request->getParsedBodyParam('title');
        $review->content = $request->getParsedBodyParam('content');
        $review->content_html = $markdown->text($request->getParsedBodyParam('content'));
        $review->who = $request->getParsedBodyParam('who');
        $review->review_date = $request->getParsedBodyParam('review_date');
        $review->approved = ($request->getParsedBodyParam('approved')) ? 'Y' : 'N';
        $review->rating = $request->getParsedBodyParam('rating');
        $review->email = $request->getParsedBodyParam('email');

        // Save
        $review = $reviewMapper->save($review);

        // Was this a review request?
        if ($request->getParsedBodyParam('review_type') === 'request') {
            $review->title = 'Review Request Sent';
            $review->token = hash('sha256', time() . $config['session']['salt']);

            // Add link to message body
            $host = $request->getUri()->getHost();
            $link = 'http://' . $host . $this->container->router->pathFor('guestReview') . "?id={$review->id}&token={$review->token}\n";
            $messageBody = $request->getParsedBodyParam('request_message');
            $messageBody .= "\n\nLink to review: {$link}";

            // Set the "from" address based on host, and strip "www."
            $host = preg_replace('/^www\./i', '', $host);

            // If sending from localhost, add .com to the end to make mail happy
            if ($host === 'localhost') {
                $host .= '.com';
            }

            // Construct message
            $message
                ->setFrom("OurSandCastle <send@{$host}>")
                ->addTo($request->getParsedBodyParam('email'))
                ->addCc($config['user']['contactEmail'])
                ->setSubject('OurSandCastle Review')
                ->setBody($messageBody);

            // Send
            $mailer->send($message);

            // Update request
            $review = $reviewMapper->save($review);
        }

        // Display admin dashboard
        return $response->withRedirect($this->container->router->pathFor('showReviews'));
    }

    /**
     * Delete Review
     */
    public function deleteReview($request, $response, $args)
    {
        // Get dependencies
        $reviewMapper = $this->container['reviewMapper'];

        $review = $reviewMapper->make();
        $review->id = (int) $args['id'];

        $reviewMapper->delete($review);

        return $response->withRedirect($this->container->router->pathFor('showReviews'));
    }

    /**
     * Show all Reviews
     */
    public function showReviews($request, $response, $args)
    {
        // Get dependencies
        $reviewMapper = $this->container['reviewMapper'];

        // Get all reviews as an admin
        $reviews = $reviewMapper->getAllReviews();

        return $this->container->view->render($response, '@admin/showReviews.html', ['reviews' => $reviews]);
    }


    /**
     * Show all Messages
     */
    public function showMessages($request, $response, $args)
    {
        // Get dependencies
        $messageMapper = $this->container['messageMapper'];

        // Get all reviews as an admin
        $messages = $messageMapper->find();

        return $this->container->view->render($response, '@admin/showMessages.html', ['messages' => $messages]);
    }

    /**
     * Delete Message
     */
    public function deleteMessage($request, $response, $args)
    {
        // Get dependencies
        $messageMapper = $this->container['messageMapper'];

        $message = $messageMapper->make();
        $message->id = (int) $args['id'];

        $messageMapper->delete($message);

        return $response->withRedirect($this->container->router->pathFor('showMessages'));
    }

    /**
     * Update Sitemap
     */
    public function updateSitemap($request, $response, $args)
    {
        // Get dependencies
        $postMapper = $this->container->get('postMapper');
        $sitemap = $this->container->get('sitemapHandler');
        $baseUrl = $this->container->get('settings')['baseUrl'];
        $sessionHandler = $this->container->get('sessionHandler');

        // Create page link array starting with home page
        $pages[] = ['link' => $baseUrl, 'date' => date('c')];

        // Other pages
        $posts = $postMapper->getPosts();
        foreach ($posts as $post) {
            $pages[] = ['link' => $baseUrl . $this->container->router->pathFor('viewPost', ['url' => $post->url]),
                'date' => date('c', strtotime($post->updated_date))];
        }

        // Make sitemap
        $sitemap->make($pages);
        $sessionHandler->setFlashData('message', 'Updated sitemap and notified search engines');

        return $response->withRedirect($this->container->router->pathFor('adminDashboard'));
    }

    /**
     * Process Post Post Data
     *
     * This gets the post data ready to save, or preview
     */
    protected function processPostData($request, $response, $args)
    {
        // Get dependencies
        $toolbox = $this->container['toolbox'];
        $sessionHandler = $this->container['sessionHandler'];
        $markdown = $this->container->get('markdownParser');

        // Validate data (simple, add validation class later)
        if ($request->getParsedBodyParam('title') === null || $request->getParsedBodyParam('url') === null) {
            // Save to session data for redisplay
            $sessionHandler->setData(['postFormData' => $request->getParsedBody()]);
            return $response->withRedirect($this->container->router->pathFor('editPost'));
        }

        // If this is a previously published post, use that publish date as default
        $publishedDate = ($request->getParsedBodyParam('published_date')) ? $request->getParsedBodyParam('published_date') : null;

        if ($request->getParsedBodyParam('button') === 'publish' && empty($publishedDate)) {
            // Then default to today
            $date = new \DateTime();
            $publishedDate = $date->format('Y-m-d');
        }

        // Assign data
        $this->post->id = $request->getParsedBodyParam('id');
        $this->post->title = $request->getParsedBodyParam('title');
        $this->post->url = $request->getParsedBodyParam('url'); // Should have been converted when title was edited in page
        $this->post->url_locked = $request->getParsedBodyParam('url_locked');
        $this->post->page = ($request->getParsedBodyParam('page')) ? 'Y' : 'N';
        $this->post->meta_description = $request->getParsedBodyParam('meta_description');
        $this->post->content = $request->getParsedBodyParam('content');
        $this->post->content_html = $markdown->text($request->getParsedBodyParam('content'));
        $this->post->template = $request->getParsedBodyParam('template');

        // Create post excerpt
        $this->post->content_excerpt = $toolbox->truncateHtmlText($this->post->content_html);

        // Only set the publish date if not empty
        if (!empty($publishedDate)) {
            $this->post->published_date = $publishedDate;
            $this->post->url_locked = 'Y';
        }

        return;
    }

    /**
     * Get Templates
     *
     * Find available theme templates
     * @return array
     */
    protected function getThemeTemplates()
    {
        // Get a list of page templates to select
        // Exclude the post.html which we'll add back as the first default element
        $templatesDir = ROOT_DIR . 'templates/' . $this->container->get('settings')['theme'];
        if (false === $files = array_diff(scandir($templatesDir), array('.', '..', 'post.html'))) {
            throw new Exception('There are no page templates to select.');
        }

        // Remove any files starting with an underscore
        $files = array_filter($files, function ($value) {
            return strpos($value, '_') === false;
        });

        // Strip off '.html' extension from files
        // $files = array_map(function ($value) {return preg_replace('/\.html/i', '', $value);}, $files);

        // If there are no templates to use, raise an error
        if (count($files) == 0) {
            throw new Exception('There are no template files to select.');
        }

        // Set default "post.html" template as first option, and alpha sort the rest
        sort($files);
        array_unshift($files, 'post.html');

        return $files;
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
            // Redirect to admin home
            return $response->withRedirect($this->container->router->pathFor('adminDashboard'));
        }

        // Get the page number and setup pagination
        $pageNumber = ($this->container->request->getParam('page')) ?: 1;
        $pagination->setPagePath($this->container->router->pathFor('adminSearch'), ['terms' => $terms]);
        $pagination->setCurrentPageNumber($pageNumber);

        $posts = $postMapper->search($terms, $pagination->getRowsPerPage(), $pagination->getOffset(), false, false);

        // Get total row count and add extension
        $pagination->setTotalRowsFound($postMapper->foundRows());
        $this->container->view->addExtension($pagination);

        // Render view
        return $this->container->view->render($response, '@admin/dashboard.html', ['posts' => $posts, 'search' => $terms]);
    }
}
