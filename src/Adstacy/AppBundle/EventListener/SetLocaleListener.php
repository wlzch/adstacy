<?php

namespace Adstacy\AppBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class SetLocaleListener
{
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $request->setLocale($request->getPreferredLanguage(array('en', 'id')));
    }
}
