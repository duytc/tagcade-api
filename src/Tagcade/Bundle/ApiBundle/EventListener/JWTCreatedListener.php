<?php

namespace Tagcade\Bundle\ApiBundle\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JWTCreatedListener
{
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        $payload = $event->getData();

        $payload['ip'] = $_SERVER['REMOTE_ADDR'];

        $event->setData($payload);
    }
}