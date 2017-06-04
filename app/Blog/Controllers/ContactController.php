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
        // Get dependencies
        $message = $this->container->mailMessage;

        // Check honeypot for spammers
        if ($request->getParsedBodyParam('alt_email') !== 'alt@example.com') {
            // Just return and say nothing
            $this->container->logger->error('Honeypot caught a fly: ' . $request->getParsedBodyParam('alt-email'));
            return $response->withRedirect($this->container->router->pathFor('thankYou'));
        }

        // Create message
        $message->setSubject('Contact message')
            ->setBody("Name: {$request->getParsedBodyParam('name')}\nEmail: {$request->getParsedBodyParam('email')}\n\n{$request->getParsedBodyParam('comment')}");

        // Send email
        $this->sendEmail($message);

        return $response->withRedirect($this->container->router->pathFor('thankYou'));
    }

    /**
     * Send Email Message
     *
     * @param Blog\Models\Comment $comment
     * @param string $subject
     * @return void
     */
    protected function sendEmail($message)
    {
        // Get dependencies
        $mailer = $this->container->sendMailMessage;
        $config = $this->container->settings;

        // Set the "from" address based on host, and strip "www."
        $host = $this->container->request->getUri()->getHost();
        $host = preg_replace('/^www\./i', '', $host);

        // If sending from localhost, add .com to the end to make mail happy
        if ($host === 'localhost') {
            $host .= '.com';
        }

        // Log email should there be an issue
        $this->container->logger->info('Sending email: ' . $message->subject . ', Body: ' . $message->body);

        // Set from and to addresses
        $message->setFrom("OurSandCastle <send@{$host}>")
            ->addTo($config['user']['contactEmail']);

        // Send
        $mailer->send($message);

        return;
    }
}
