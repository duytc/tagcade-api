<?php

namespace Tagcade\Security\Authorization\Voter;

use Tagcade\Model\Core\DisplayBlacklistInterface;
use Tagcade\Model\User\UserEntityInterface;

class DisplayBlacklistVoter extends EntityVoterAbstract
{
    public function supportsClass($class)
    {
        $supportedClass = DisplayBlacklistInterface::class;

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param DisplayBlacklistInterface $blacklist
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isPublisherActionAllowed($blacklist, UserEntityInterface $user, $action)
    {
        return true;
    }

    /**
     * @param DisplayBlacklistInterface $blacklist
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isSubPublisherActionAllowed($blacklist, UserEntityInterface $user, $action)
    {
        return true;
    }
}