<?php

namespace Tagcade\Security\Authorization\Voter;

use Tagcade\Model\User\UserEntityInterface;
use Tagcade\Model\Core\AdSlotInterface;

class AdSlotVoter extends EntityVoterAbstract
{
    public function supportsClass($class)
    {
        $supportedClass = AdSlotInterface::class;

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param AdSlotInterface $adSlot
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isPublisherActionAllowed($adSlot, UserEntityInterface $user, $action)
    {
        return $user->getId() == $adSlot->getSite()->getPublisherId();
    }
}