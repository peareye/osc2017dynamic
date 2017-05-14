<?php
/**
 * Contact Controller
 */
namespace Blog\Controllers;

use Blog\Models\Comment;

class ContactController extends BaseController
{
    /**
     * Send Contact Email
     *
     */
    public function sendContactEmail($request, $response, $args)
    {
        // Check honeypot for spammers
        if ($request->getParsedBodyParam('alt-email') !== 'alt@example.com') {
            // Just return and say nothing
            $this->container->view->render($response, '_thankYou.html');
        }

        // Verify we have required fields
        // if (!$request->getParsedBodyParam('name') || !$request->getParsedBodyParam('email') || !$request->getParsedBodyParam('comment')) {
        //     // Return error
        //     //return $r->write(json_encode(["status" => "1", "source" => "<p class=\"bg-danger\">Error</p>"]));
        // }

        // Create message
        $message = new \stdClass();
        $message->name = $request->getParsedBodyParam('name');
        $message->email = $request->getParsedBodyParam('email');
        $message->comment = $request->getParsedBodyParam('comment');

        // Email admin with new comment and post title
        $this->sendEmail('Contact message', "Name: {$message->name}\nEmail: {$message->email}\n\n{$message->comment}");

        // Return
        return;
    }

    /**
     * Send Email Message
     *
     * @param Blog\Models\Comment $comment
     * @param string $subject
     * @return void
     */
    protected function sendEmail($subject = '', $body = '')
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
        $message->setFrom("OurSandCastle <send@{$host}>")
            ->addTo($config['user']['contactEmail'])
            ->setSubject($subject)
            ->setBody($body);

        // Send
        $mailer->send($message);

        return;
    }
}
