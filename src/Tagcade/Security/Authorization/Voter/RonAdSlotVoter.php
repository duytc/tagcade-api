<?php

namespace Tagcade\Security\Authorization\Voter;

use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Model\User\UserEntityInterface;

class RonAdSlotVoter extends EntityVoterAbstract
{
    public function supportsClass($class)
    {
        $supportedClass = RonAdSlotInterface::class;

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param RonAdSlotInterface $ronAdSlot
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isPublisherActionAllowed($ronAdSlot, UserEntityInterface $user, $action)
    {
        return $user->getId() == $ronAdSlot->getLibraryAdSlot()->getPublisherId();
    }
}