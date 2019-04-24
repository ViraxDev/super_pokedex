<?php

declare(strict_types=1);

namespace App\EventListener;

use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ExceptionListener
 * @package App\EventListener
 */
class ExceptionListener
{
    /**
     * @var Request|null
     */
    private $request;

    public function __construct(RequestStack $request)
    {
        $this->request = $request->getCurrentRequest();
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event): void
    {
        // You get the exception object from the received event
        $exception = $event->getException();

        // Customize your response object to display the exception details
        $response = new JsonResponse([
            'error' => $exception->getMessage()
        ]);


        if ($exception instanceof HttpExceptionInterface) {

            $response = $this->throwException($exception, $response);

            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
        } else {
            $response->setStatusCode(JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }


        // sends the modified response object to the event
        $event->setResponse($response);
    }


    private function throwException(Exception $exception, JsonResponse $jsonResponse): JsonResponse
    {

        $id = $this->request->get('id');

        switch ($exception) {
            case $exception instanceof NotFoundHttpException && false !== $id:
                $jsonResponse = new JsonResponse([
                    'error' => "The object with id $id not found"
                ]);
                break;
        }

        return $jsonResponse;
    }
}
