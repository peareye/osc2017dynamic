<?php
/**
 * Contact Controller
 */
namespace App\Controllers;

use App\Models\Comment;

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
        $messageMapper = $this->container->messageMapper;
        $messageDb = $messageMapper->make();

        // Check honeypot for spammers
        if ($request->getParsedBodyParam('alt_email') !== 'alt@example.com') {
            // Just return and say nothing
            $this->container->logger->error('Honeypot caught a fly: ' . $request->getParsedBodyParam('alt-email'));
            return $response->withRedirect($this->container->router->pathFor('thankYou', ['type' => 'contact']));
        }

        // Create message
        $message->setSubject('Contact Message')
            ->setBody("Name: {$request->getParsedBodyParam('name')}\nEmail: {$request->getParsedBodyParam('email')}\n\n{$request->getParsedBodyParam('comment')}");

        // Save to database
        $messageDb->name = $request->getParsedBodyParam('name');
        $messageDb->email = $request->getParsedBodyParam('email');
        $messageDb->text = $message->getBody();
        $messageMapper->save($messageDb);

        // Send email
        $this->sendEmail($message);

        return $response->withRedirect($this->container->router->pathFor('thankYou', ['type' => 'contact']));
    }

    /**
     * Send Reservation Email
     *
     */
    public function sendReservationEmail($request, $response, $args)
    {
        // Get dependencies
        $message = $this->container->mailMessage;
        $messageMapper = $this->container->messageMapper;
        $messageDb = $messageMapper->make();

        // Check honeypot for spammers
        if ($request->getParsedBodyParam('alt_email') !== 'alt@example.com') {
            // Just return and say nothing
            $this->container->logger->error('Honeypot caught a fly: ' . $request->getParsedBodyParam('alt-email'));
            return $response->withRedirect($this->container->router->pathFor('thankYou', ['type' => 'reserve']));
        }

        $bodyText = <<<EOT
Name: {$request->getParsedBodyParam('last_name')}\n
Email: {$request->getParsedBodyParam('email')}
Phone Number: {$request->getParsedBodyParam('phone_number')}\n
Address: {$request->getParsedBodyParam('address1')}
Address: {$request->getParsedBodyParam('address2')}
City: {$request->getParsedBodyParam('city')}
State/Province: {$request->getParsedBodyParam('state')}
Zip/Postal Code: {$request->getParsedBodyParam('postal_code')}
Country: {$request->getParsedBodyParam('country')}\n
Number in Party: {$request->getParsedBodyParam('party_count')}\n
Arrival Date: {$request->getParsedBodyParam('arrival_date')}
Departure Date: {$request->getParsedBodyParam('departure_date')}\n
{$request->getParsedBodyParam('message')}
EOT;

        // Create message
        $message->setSubject('Reservation Request')->setBody($bodyText);

        // Save to database
        $messageDb->name = $request->getParsedBodyParam('last_name');
        $messageDb->email = $request->getParsedBodyParam('email');
        $messageDb->text = $message->getBody();
        $messageMapper->save($messageDb);

        // Send email
        $this->sendEmail($message);

        return $response->withRedirect($this->container->router->pathFor('thankYou', ['type' => 'reserve']));
    }

    /**
     * Send Email Message
     *
     * @param App\Models\Comment $comment
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
