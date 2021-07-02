<?php



namespace App\Security\exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class Exception

{
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        $response = new Response();
        // setup the Response object based on the caught exception
        $event->setResponse($response);

        // you can alternatively set a new Exception
        // $exception = new \Exception('Some special exception');
        // $event->setThrowable($exception);
    }
}