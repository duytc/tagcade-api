<?php

namespace Tagcade\Security\Authorization\Voter;

use Tagcade\Model\Core\DynamicAdSlotInterface;
use Tagcade\Model\User\UserEntityInterface;
use Tagcade\Model\Core\AdSlotInterface;

class DynamicAdSlotVoter extends EntityVoterAbstract
{
    public function supportsClass($class)
    {
        $supportedClass = DynamicAdSlotInterface::class;

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param DynamicAdSlotInterface $adSlot
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isPublisherActionAllowed($adSlot, UserEntityInterface $user, $action)
    {
        return $user->getId() == $adSlot->getSite()->getPublisherId();
    }
}