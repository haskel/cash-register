<?php

namespace App\Core\Http\Listener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use JsonException;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsEventListener(event: KernelEvents::REQUEST, method: 'onKernelRequest', priority: 100)]
class RequestListener
{
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (false === $this->isAvailable($request)) {
            return;
        }

        if (false === $this->transform($request)) {
            $response = new Response('Unable to parse request.', Response::HTTP_BAD_REQUEST);

            $event->setResponse($response);
        }
    }

    private function isAvailable(Request $request): bool
    {
        return 'json' === $request->getContentType() && $request->getContent();
    }

    private function transform(Request $request): bool
    {
        try {
            $data = json_decode((string) $request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            return false;
        }

        if (JSON_ERROR_NONE !== json_last_error()) {
            return false;
        }

        if (is_array($data)) {
            $request->request->replace($data);
        }

        return true;
    }
}
