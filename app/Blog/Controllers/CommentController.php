<?php
/**
 * Comment Controller
 */
namespace Blog\Controllers;

use Blog\Models\Comment;

class CommentController extends BaseController
{
    /**
     * Show Comments
     */
    public function showAll($request, $response, $args)
    {
        // Get dependencies
        $commentMapper = $this->container['commentMapper'];

        // Get all comments, approved or not
        $comments = $commentMapper->getAllComments();

        return $this->container->view->render($response, '@admin/comments.html', ['comments' => $comments]);
    }

    /**
     * Save Comment
     *
     * Using Ajax-y
     */
    public function save($request, $response, $args)
    {
        // Get dependencies
        $commentMapper = $this->container['commentMapper'];
        $comment = $commentMapper->make();

        // Set the response type and render thank you view
        $r = $response->withHeader('Content-Type', 'application/json');
        $source = $this->container->view->fetch('_thankYou.html');

        // Check honeypot for spammers
        if ($request->getParsedBodyParam('alt-email') !== 'alt@example.com') {
            // Just return and say nothing
            $this->container->logger->info('Comment honeypot caught a fly: ' . $request->getParsedBodyParam('alt-email'));
            return $r->write(json_encode(["status" => "1", "source" => "$source"]));
        }

        // Verify we have required fields
        if (!$request->getParsedBodyParam('name') || !$request->getParsedBodyParam('email') || !$request->getParsedBodyParam('comment')) {
            // Return error
            return $r->write(json_encode(["status" => "1", "source" => "<p class=\"bg-danger\">Error</p>"]));
        }

        // Create comment
        $comment->reply_id = $request->getParsedBodyParam('reply_id');
        $comment->post_id = $request->getParsedBodyParam('post_id');
        $comment->reply_id = $request->getParsedBodyParam('reply_id');
        $comment->name = $request->getParsedBodyParam('name');
        $comment->email = $request->getParsedBodyParam('email');
        $comment->comment = $request->getParsedBodyParam('comment');

        // Save comment, or send contact me
        if (is_numeric($comment->post_id)) {
            $commentMapper->save($comment);
            $subject = 'A new comment on your blog is awaiting approval';
        } else {
            $subject = 'Blog contact message';
        }

        // Email admin with new comment and post title
        $comment->post_title = $request->getParsedBodyParam('post_title');
        $this->sendNewCommentEmail($comment, $subject);

        // Return
        return $r->write(json_encode(["status" => "1", "source" => "$source"]));
    }

    /**
     * Change Comment Status
     */
    public function changeCommentStatus($request, $response, $args)
    {
        // Get dependencies
        $commentMapper = $this->container['commentMapper'];
        $comment = $commentMapper->make();

        // Set value and save
        $comment->id = $args['commentId'];
        $comment->approved = $args['newStatus'];
        $commentMapper->save($comment);

        return $response->withRedirect($this->container->router->pathFor('comments'));
    }

    /**
     * Delete Comment
     */
    public function deleteComment($request, $response, $args)
    {
        // Get dependencies
        $commentMapper = $this->container['commentMapper'];
        $comment = $commentMapper->make();

        $comment->id = $args['commentId'];
        $commentMapper->delete($comment);

        return $response->withRedirect($this->container->router->pathFor('comments'));
    }

    /**
     * Send New Comment Email
     *
     * Sends comment or contact email
     * @param Blog\Models\Comment $comment
     * @param string $subject
     * @return void
     */
    protected function sendNewCommentEmail(Comment $comment, $subject = '')
    {
        // Get dependencies
        $message = $this->container->get('mailMessage');
        $mailer = $this->container->get('sendMailMessage');
        $config = $this->container->get('settings');

        // Set the "from" address based on host, and strip "www."
        $host = $this->container->request->getUri()->getHost();
        $host = preg_replace('/^www\./i', '', $host);

        // If sending from localhost, add .com to the end to make mail happy
        if ($host === 'localhost') {
            $host .= '.com';
        }

        // Create message
        $message->setFrom("My Blog <send@{$host}>")
            ->addTo($config['user']['email'])
            ->setSubject($subject)
            ->setBody("Name: {$comment->name}\nEmail: {$comment->email}\nPost Title: {$comment->post_title}\n\n{$comment->comment}");

        // Send
        $mailer->send($message);

        return;
    }
}
