<?php


namespace App\EventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        /**
         * Get code
         */
        if (!$exception->getCode() && $exception instanceof HttpExceptionInterface) {
            $code = $exception->getStatusCode();
        } elseif (!$exception->getCode()) {
            $code = Response::HTTP_INTERNAL_SERVER_ERROR;
        } else {
            $code = $exception->getCode();
        }


        /**
         * Construct response
         */
        $result['body'] = [
            'code' => $code,
            'message' => $exception->getMessage()
        ];


        $response = new JsonResponse($result['body'], $code);

        $event->setResponse($response);
    }


}