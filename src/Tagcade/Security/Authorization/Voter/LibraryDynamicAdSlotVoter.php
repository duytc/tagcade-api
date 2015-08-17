<?php

namespace Tagcade\Security\Authorization\Voter;

use Tagcade\Model\Core\LibraryDynamicAdSlotInterface;
use Tagcade\Model\User\UserEntityInterface;

class LibraryDynamicAdSlotVoter extends EntityVoterAbstract
{
    public function supportsClass($class)
    {
        $supportedClass = LibraryDynamicAdSlotInterface::class;

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param LibraryDynamicAdSlotInterface $libraryDynamicAdSlot
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isPublisherActionAllowed($libraryDynamicAdSlot, UserEntityInterface $user, $action)
    {
        return $user->getId() == $libraryDynamicAdSlot->getPublisherId();
    }
}