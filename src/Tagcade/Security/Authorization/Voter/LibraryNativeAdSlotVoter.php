<?php

namespace Tagcade\Security\Authorization\Voter;

use Tagcade\Model\Core\LibraryNativeAdSlotInterface;
use Tagcade\Model\User\UserEntityInterface;

class LibraryNativeAdSlotVoter extends EntityVoterAbstract
{
    public function supportsClass($class)
    {
        $supportedClass = LibraryNativeAdSlotInterface::class;

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param LibraryNativeAdSlotInterface $libraryNativeAdSlot
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isPublisherActionAllowed($libraryNativeAdSlot, UserEntityInterface $user, $action)
    {
        return $user->getId() == $libraryNativeAdSlot->getPublisherId();
    }
}