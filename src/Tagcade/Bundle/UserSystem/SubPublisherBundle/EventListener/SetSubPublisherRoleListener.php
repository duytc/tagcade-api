<?php

namespace Tagcade\Bundle\UserSystem\SubPublisherBundle\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use Tagcade\Model\User\Role\SubPublisherInterface;
use Tagcade\Model\User\UserEntityInterface;

class SetSubPublisherRoleListener
{
    const ROLE_SUB_PUBLISHER = 'ROLE_SUB_PUBLISHER';

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof SubPublisherInterface) {
            return;
        }

        /** @var UserEntityInterface $entity */
        $entity->setUserRoles(array(static::ROLE_SUB_PUBLISHER));
    }
}