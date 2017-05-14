<?php
/**
 * Index Controller
 */
namespace Blog\Controllers;

class IndexController extends BaseController
{
    /**
     * Get Home Page
     */
    public function home($request, $response, $args)
    {
        // Get dependencies
        $postMapper = $this->container->get('postMapper');

        // Render view
        $this->container->view->render($response, 'home.html');
    }

    /**
     * View Post
     */
    public function viewPost($request, $response, $args)
    {
        $postMapper = $this->container['postMapper'];
        $post = $postMapper->getSinglePost($args['url']);

        // Was anything found?
        if (empty($post)) {
            return $this->notFound($request, $response);
        }

        // Make sure we have a template set
        $template = (!$post->template) ? 'post.html' : $post->template;

        $this->container->view->render($response, $template, ['post' => $post, 'metaDescription' => $post->meta_description]);
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
     * Contact Me Form
     *
     * Renders contact me form
     */
    public function contact($request, $response, $args)
    {
        // Render view
        return $this->container->view->render($response, 'contact.html', ['title' => 'Contact Form']);
    }
}
