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

        $page['welcome'] = $postMapper->getSinglePost(1);
        $page['house'] = $postMapper->getSinglePost(2);
        $page['provide'] = $postMapper->getSinglePost(3);
        $page['comfort'] = $postMapper->getSinglePost(4);
        $page['review'] = $postMapper->getSinglePost(5);

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
}
