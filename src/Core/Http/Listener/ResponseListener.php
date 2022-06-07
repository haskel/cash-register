<?php

namespace App\Core\Http\Listener;

use App\Core\Http\ExceptionHttpMapInterface;
use App\Dto\ErrorResponse;
use App\Dto\ValidationError;
use App\Dto\ValidationErrorResponse;
use App\Exception\PublicException;
use App\Exception\ValidationException;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;

/**
 * Serializes response to json and prepares public exception message.
 */
#[AsEventListener(event: KernelEvents::VIEW, method: 'onKernelView')]
#[AsEventListener(event: KernelEvents::EXCEPTION, method: 'onKernelException')]
class ResponseListener
{
    private bool $showFullError;

    public function __construct(
        #[Autowire('%env(APP_ENV)%')]
        string $env,
        private SerializerInterface $serializer,
        private LoggerInterface $logger,
        private ExceptionHttpMapInterface $exceptionHttpMap,
    ) {
        $this->showFullError = 'dev' === strtolower($env);
    }

    public function onKernelView(ViewEvent $event): void
    {
        $response = $this->serializer->serialize($event->getControllerResult(), 'json');
        $event->setResponse(new JsonResponse($response, Response::HTTP_OK, [], true));
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $errorId = sprintf('%s-%s', random_int(0, (int) 10e7), microtime(true));
        $trace = $this->showFullError
            ? $this->removeArgsFromTrace($exception->getTrace())
            : [];
        $payload = new ErrorResponse($exception->getMessage(), $errorId, $trace);

        if (!$exception instanceof PublicException && !$this->showFullError) {
            $this->logger->error(
                $exception->getMessage(),
                [
                    'trace' => $exception->getTrace(),
                    'errorId' => $errorId,
                ]
            );

            $payload = new ErrorResponse(sprintf('Something went wrong. Error ID: %s', $errorId), $errorId);
        }

        if ($exception instanceof ValidationException) {
            $errors = [];
            /** @var ConstraintViolation $error */
            foreach ($exception->getErrors() as $error) {
                $errors[] = new ValidationError((string) $error->getMessage(), $error->getPropertyPath());
            }

            $payload = new ValidationErrorResponse($exception->getMessage(), $errors);
        }

        $response = new JsonResponse(
            $this->serializer->serialize($payload, 'json'),
            Response::HTTP_BAD_REQUEST,
            [],
            true
        );

        if ($httpCode = $this->exceptionHttpMap->getCode($exception)) {
            $response->setStatusCode($httpCode);
        }

        if ($exception instanceof HttpExceptionInterface) {
            $response->setStatusCode($exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
        }

        $event->setResponse($response);
    }

    /**
     * @param array<int, array<string, mixed>> $trace
     *
     * @return array<int, array<string, mixed>>
     */
    private function removeArgsFromTrace(array $trace = []): array
    {
        array_walk(
            $trace,
            static function (array &$stackData): void {
                unset($stackData['args']);
            },
        );

        return $trace;
    }
}
