<?php

declare(strict_types=1);

namespace App\Infrastructure\EventListener;

use App\Infrastructure\Exception\NotFoundException;
use App\Infrastructure\Exception\ValidationException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[AsEventListener(event: 'kernel.exception')]
final readonly class ExceptionListener
{
    public function __construct(
        #[Autowire('%kernel.environment%')]
        private string $environment
    ) {
    }

    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $content = [];
        $code = Response::HTTP_INTERNAL_SERVER_ERROR;

        if ($exception instanceof HttpException && $exception->getPrevious() instanceof ValidationFailedException) {
            /** @var ValidationFailedException $validation */
            $validation = $exception->getPrevious();

            foreach ($validation->getViolations() as $violation) {
                $content['errors'][$violation->getPropertyPath()][] = $violation->getMessage();
            }

            $code = Response::HTTP_UNPROCESSABLE_ENTITY;
        } elseif ($exception instanceof ValidationException) {
            $content['message'] = $exception->getMessage();
            $code = Response::HTTP_BAD_REQUEST;
        } elseif ($exception instanceof NotFoundHttpException || $exception instanceof NotFoundException) {
            $code = Response::HTTP_NOT_FOUND;
            $content['message'] = $exception->getMessage() ?: 'Resource not found';
        } elseif ($exception instanceof MethodNotAllowedHttpException) {
            $code = Response::HTTP_METHOD_NOT_ALLOWED;
            $content['message'] = $exception->getMessage() ?: 'Method not allowed';
        } elseif ($exception instanceof HttpException) {
            $code = $exception->getStatusCode();
            $content['message'] = $exception->getMessage();
        } else {
            if ($this->environment === 'dev') {
                $content = [
                    'message' => $exception->getMessage(),
                    'class' => get_class($exception),
                    'trace' => $exception->getTraceAsString(),
                ];
            } else {
                $content['message'] = 'Internal Server Error';
            }
        }

        $event->setResponse(new JsonResponse($content, $code));
    }
}
