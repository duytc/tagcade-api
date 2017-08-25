<?php

namespace Tagcade\Security\Authorization\Voter;

use Tagcade\Model\Core\IvtPixelInterface;
use Tagcade\Model\User\UserEntityInterface;

class IvtPixelVoter extends EntityVoterAbstract
{
    public function supportsClass($class)
    {
        $supportedClass = IvtPixelInterface::class;

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param IvtPixelInterface $ivtPixel
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isPublisherActionAllowed($ivtPixel, UserEntityInterface $user, $action)
    {
        return $user->getId() == $ivtPixel->getPublisher()->getId();
    }

    /**
     * @param IvtPixelInterface $ronAdSlot
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isSubPublisherActionAllowed($ronAdSlot, UserEntityInterface $user, $action)
    {
        // not allowed
        return false;
    }
}