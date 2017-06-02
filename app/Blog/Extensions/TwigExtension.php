<?php
/**
 * Custom Extensions for Twig
 */
namespace Blog\Extensions;

use Interop\Container\ContainerInterface;

class TwigExtension extends \Twig_Extension
{
    /**
     * @var \Slim\Interfaces\RouterInterface
     */
    private $router;

    /**
     * @var string|\Slim\Http\Uri
     */
    private $uri;

    /**
     * @var Interop\Container\ContainerInterface
     */
    private $container;

    /**
     * Application Settings
     * @var array
     */
    private $settings = [];

    public function __construct(ContainerInterface $container)
    {
        $this->router = $container['router'];
        $this->uri = $container['request']->getUri();
        $this->container = $container;
        $this->settings = $container->get('settings');
    }

    // Identifer
    public function getName()
    {
        return 'blog';
    }

    /**
     * Register Global variables
     */
    public function getGlobals()
    {
        return [
            'setting' => $this->settings,
            'theme' => $this->getThemeName(),
        ];
    }

    /**
     * Register Custom Filters
     */
    public function getFilters()
    {
        return [];
    }

    /**
     * Register Custom Functions
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('pathFor', array($this, 'pathFor')),
            new \Twig_SimpleFunction('baseUrl', array($this, 'baseUrl')),
            new \Twig_SimpleFunction('basePath', array($this, 'getBasePath')),
            new \Twig_SimpleFunction('inUrl', array($this, 'isInUrl')),
            new \Twig_SimpleFunction('getPostArchive', array($this, 'getPostArchiveNavigation')),
            new \Twig_SimpleFunction('theme', array($this, 'getThemeName')),
            new \Twig_SimpleFunction('authenticated', array($this, 'authenticated')),
            new \Twig_SimpleFunction('imageSize', array($this, 'getImageSize')),
            new \Twig_SimpleFunction('postComments', array($this, 'getPostComments')),
            new \Twig_SimpleFunction('newCommentCount', array($this, 'getNewCommentCount')),
            new \Twig_SimpleFunction('checked', array($this, 'checked')),
            new \Twig_SimpleFunction('pages', array($this, 'getPages')),
            new \Twig_SimpleFunction('nextPost', array($this, 'getPriorAndNextPosts')),
            new \Twig_SimpleFunction('priorPost', array($this, 'getPriorPost')),
            new \Twig_SimpleFunction('postCommentCount', array($this, 'getCommentCountByPostId')),
            new \Twig_SimpleFunction('flashMessage', array($this, 'getFlashMessage')),
            new \Twig_SimpleFunction('getReviews', array($this, 'getReviews')),
        ];
    }

    /**
     * Get Path for Named Route
     *
     * @param string $name Name of the route
     * @param array $data Associative array to assign to route segments
     * @param array $queryParams Query string parameters
     * @return string The desired route path without the domain, but does include the basePath
     */
    public function pathFor($name, $data = [], $queryParams = [])
    {
        return $this->router->pathFor($name, $data, $queryParams);
    }

    /**
     * Base URL
     *
     * Returns the base url including scheme, domain, port, and base path
     * @param none
     * @return string The base url
     */
    public function baseUrl()
    {
        if (is_string($this->uri)) {
            return $this->uri;
        }

        if (method_exists($this->uri, 'getBaseUrl')) {
            return $this->uri->getBaseUrl();
        }
    }

    /**
     * Base Path
     *
     * If the application is run from a directory below the project root
     * this will return the subdirectory path.
     * Use this instead of baseUrl to use relative URL's instead of absolute
     * @param void
     * @return string The base path segments
     */
    public function getBasePath()
    {
        if (method_exists($this->uri, 'getBasePath')) {
            return $this->uri->getBasePath();
        }
    }

    /**
     * In URL
     *
     * Checks if the supplied string is one of the URL segments
     * @param string $segment URL segment to find
     * @return boolean
     */
    public function isInUrl($segmentToTest = null)
    {
        // Verify we have a segment to find
        if ($segmentToTest === null) {
            return false;
        }

        // If just a slash is provided, meaning 'home', then evaluate
        if ($segmentToTest === '/' && ($this->uri->getPath() === '/' || empty($this->uri->getPath()))) {
            return true;
        } else if ($segmentToTest === '/' && !empty($this->uri->getPath())) {
            return false;
        }

        // Clean segment of slashes
        $segmentToTest = trim($segmentToTest, '/');

        return in_array($segmentToTest, explode('/', $this->uri->getPath()));
    }

    /**
     * Get Post Archives
     */
    public function getPostArchiveNavigation()
    {
        // Get dependency and all posts
        $postMapper = $this->container->get('postMapper');
        $posts = $postMapper->getPosts();

        // Create array with posts nested by publish year and month
        $nav = [];
        foreach ($posts as $post) {
            $time = strtotime($post->published_date);
            $nav[date('Y', $time)][date('F', $time)][] = ['title' => $post->title, 'url' => $post->url];
        }

        return $nav;
    }

    /**
     * Get Theme Name
     *
     * @param void
     * @return string
     */
    public function getThemeName()
    {
        return ($this->settings['theme']) ?: 'default';
    }

    /**
     * Authenticated
     *
     * Is the user authenticated?
     * @return boolean
     */
    public function authenticated()
    {
        $security = $this->container->securityHandler;

        return $security->authenticated();
    }

    /**
     * Get Image Size
     *
     * @param string $path Path to image
     * @return array "width", "height", "aspect"
     */
    public function getImageSize($imagePath)
    {
        $filePath = $this->settings['file']['filePath'];
        $size = getimagesize($filePath . $imagePath);

        if (!$size || $size[1] === 0) {
            return null;
        }

        return ['width' => $size[0], 'height' => $size[1], 'aspect' => round($size[0] / $size[1], 4)];
    }

    /**
     * Get Post Comments
     *
     * Returns approved comments in a nested array
     * @param int $postId
     * @return array
     */
    public function getPostComments($postId)
    {
        // Get dependencies and comments
        $commentMapper = $this->container->get('commentMapper');
        $comments = $commentMapper->getPostComments($postId);

        // If no comment were found, stop here
        if (empty($comments)) {
            return false;
        }

        // Reindex array by comment ID
        $indexedComments = [];
        foreach ($comments as $row) {
            $indexedComments[$row->id] = $row;
        }

        $nestedComments = [];
        foreach ($indexedComments as &$c) {
            if ($c->reply_id === 0) {
                // Top level comment so put it in the root
                $nestedComments[] = &$c;
            } else {
                if (isset($indexedComments[$c->reply_id])) {
                    // If the parent ID exists in the comments array add it to the 'replies' property of the parent
                    if (!isset($indexedComments[$c->reply_id]->replies)) {
                        $indexedComments[$c->reply_id]->replies = [];
                    }

                    $indexedComments[$c->reply_id]->replies[] = &$c;
                }
            }
        }

        return $nestedComments;
    }

    /**
     * Get Comment Count by Post ID
     *
     * @param int $postId Post record ID
     * @return int
     */
    public function getCommentCountByPostId($postId)
    {
        static $postCommentCount = [];

        // Return cached value if we have it
        if (isset($postCommentCount[$postId])) {
            return $postCommentCount[$postId];
        }

        // Otherwise, get the comment count and return after adding to cache
        $commentMapper = $this->container['commentMapper'];

        return $postCommentCount[$postId] = $commentMapper->getCommentCountByPostId($postId);
    }

    /**
     * Get New Comment Count
     *
     * @return int
     */
    public function getNewCommentCount()
    {
        $commentMapper = $this->container['commentMapper'];

        return $commentMapper->getNewCommentCount();
    }

    /**
     * Set Checkbox
     *
     * If the supplied value is truthy, 1, or 'Y' returns the checked string
     * @param mixed $value
     * @return string
     */
    public function checked($value = 0)
    {
        return ($value == 1 || $value == 'Y') ? 'checked="checked"' : '';
    }

    /**
     * Get All Pages
     *
     * Gets all posts marked as a page for navigation
     * @return array
     */
    public function getPages()
    {
        $pageMapper = $this->container['postMapper'];

        return $pageMapper->getPages();
    }

    /**
     * Get Prior and Next Posts
     *
     * Get the post before and after the current post
     * @param mixed $currentPost Post URL or ID
     * @param string $which 'prior' or 'next'
     * @return string
     */
    public function getPriorAndNextPosts($currentPost, $which = 'next')
    {
        // Using a static to cache the result
        static $adjoiningPosts = [];

        // Fetch if not yet set
        if (!$adjoiningPosts) {
            $pageMapper = $this->container['postMapper'];
            $posts = $pageMapper->getPriorAndNextPosts($currentPost);

            // Assign return values
            $adjoiningPosts['prior'] = isset($posts->priorPost) ? $posts->priorPost : null;
            $adjoiningPosts['next'] = isset($posts->nextPost) ? $posts->nextPost : null;
        }

        // Return the desired value, but only return URL string if a post URL exists
        if ($adjoiningPosts[$which] !== null) {
            return $this->container->router->pathFor('viewPost', ['url' => $adjoiningPosts[$which]]);
        }

        return null;
    }

    /**
     * Get Prior Post
     *
     * Wrapper to call $this->getPriorAndNextPosts() for prior value
     * @param mixed $currentPost Post URL or ID
     * @return string
     */
    public function getPriorPost($currentPost)
    {
        return $this->getPriorAndNextPosts($currentPost, 'prior');
    }

    /**
     * Get Flash Messages
     *
     * If a message key is provided, then that message is returned.
     * If no key is provided, all messages are returned in an unordered list.
     * @param string $key Optional array key of message
     * @return array
     */
    public function getFlashMessage($key = null)
    {
        static $messages;

        if (!$messages) {
            $session = $this->container->sessionHandler;
            $messages = $session->getFlashData();
        }

        // If we have no messages, then return nothing
        if (empty($messages)) {
            return null;
        }

        // If a key was provided, return that flash data element
        if ($key !== null) {
            return isset($messages[$key]) ? $messages[$key] : null;
        }

        // Return all messages as unordered list
        return '<ul><li>' . implode('</li><li>', $messages) . '</li></ul>';
    }

    /**
     * Get Reviews
     *
     * @param int|null $limit Number of recent reviews, all reviews if null
     * @return array Array of reviews
     */
    public function getReviews($limit = null)
    {
        // Get dependencies
        $reviewMapper = $this->container['reviewMapper'];

        return $reviewMapper->getReviews($limit);
    }
}
