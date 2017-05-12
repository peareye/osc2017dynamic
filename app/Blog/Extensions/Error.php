<?php
/**
 * Custom Error Handler
 */
namespace Blog\Extensions;

use Monolog\Logger;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Error extends \Slim\Handlers\Error
{
    protected $logger;

    public function __construct($displayErrorDetails, Logger $logger)
    {
        $this->logger = $logger;
        parent::__construct($displayErrorDetails);
    }

    public function __invoke(Request $request, Response $response, \Exception $exception)
    {
        // Log the message
        $this->logger->critical($exception->getMessage() . ' ' . $exception->getTraceAsString());

        return parent::__invoke($request, $response, $exception);
    }
}
