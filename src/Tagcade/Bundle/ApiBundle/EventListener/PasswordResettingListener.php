<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tagcade\Bundle\ApiBundle\EventListener;

use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Tagcade\Bundle\UserSystem\PublisherBundle\TagcadeUserSystemPublisherEvents;

class PasswordResettingListener implements EventSubscriberInterface
{
    private $router;
    private $tokenTtl;

    public function __construct(UrlGeneratorInterface $router, $tokenTtl)
    {
        $this->router = $router;
        $this->tokenTtl = $tokenTtl;
    }

    public static function getSubscribedEvents()
    {
        return array(
            TagcadeUserSystemPublisherEvents::RESETTING_RESET_SUCCESS => 'onResettingResetSuccess'
        );
    }

    public function onResettingResetSuccess(FormEvent $event)
    {
        /** @var $user \FOS\UserBundle\Model\UserInterface */
        $user = $event->getForm()->getData();

        $user->setConfirmationToken(null);
        $user->setPasswordRequestedAt(null);
        $user->setEnabled(true);
    }
}
