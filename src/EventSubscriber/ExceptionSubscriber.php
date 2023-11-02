<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly RouterInterface $router)
    {

    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => [
                ['onKernelException', 0],
            ],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        // $url = $this->router->generate('index');
        // $response = new RedirectResponse($url);

        // $event->setResponse($response);
    }
}