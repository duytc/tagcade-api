<?php

namespace Tagcade\Security\Authorization\Voter;

use Tagcade\Model\Core\DisplayWhiteListInterface;
use Tagcade\Model\User\UserEntityInterface;

class DisplayWhiteListVoter extends EntityVoterAbstract
{
    public function supportsClass($class)
    {
        $supportedClass = DisplayWhiteListInterface::class;

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param DisplayWhiteListInterface $blacklist
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isPublisherActionAllowed($blacklist, UserEntityInterface $user, $action)
    {
        return true;
    }

    /**
     * @param DisplayWhiteListInterface $blacklist
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isSubPublisherActionAllowed($blacklist, UserEntityInterface $user, $action)
    {
        return true;
    }
}