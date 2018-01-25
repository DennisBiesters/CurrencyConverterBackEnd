<?php

namespace AppBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class KernelListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::RESPONSE => 'onKernelResponse'
        );
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $httpRequestOrigin = $event->getRequest()->headers->get('origin');

        $event->getResponse()->headers->set('Access-Control-Allow-Origin', $httpRequestOrigin);
        $event->getResponse()->headers->set('Access-Control-Allow-Credentials', 'true');
    }
}