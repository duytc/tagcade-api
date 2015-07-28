<?php

namespace Tagcade\Security\Authorization\Voter;

use Tagcade\Model\Core\LibraryDisplayAdSlotInterface;
use Tagcade\Model\User\UserEntityInterface;

class LibraryDisplayAdSlotVoter extends EntityVoterAbstract
{
    public function supportsClass($class)
    {
        $supportedClass = LibraryDisplayAdSlotInterface::class;

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param LibraryDisplayAdSlotInterface $libraryDisplayAdSlot
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isPublisherActionAllowed($libraryDisplayAdSlot, UserEntityInterface $user, $action)
    {
        return $user->getId() == $libraryDisplayAdSlot->getPublisherId();
    }
}