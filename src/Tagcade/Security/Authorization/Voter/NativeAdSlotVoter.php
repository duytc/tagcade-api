<?php

namespace Tagcade\Security\Authorization\Voter;

use Tagcade\Model\Core\NativeAdSlotInterface;
use Tagcade\Model\User\UserEntityInterface;

class NativeAdSlotVoter extends EntityVoterAbstract
{
    public function supportsClass($class)
    {
        $supportedClass = NativeAdSlotInterface::class;

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param NativeAdSlotInterface $nativeAdSlot
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isPublisherActionAllowed($nativeAdSlot, UserEntityInterface $user, $action)
    {
        return $user->getId() == $nativeAdSlot->getSite()->getPublisherId();
    }
}