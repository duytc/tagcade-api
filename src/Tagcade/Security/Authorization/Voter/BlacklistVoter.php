<?php

namespace Tagcade\Security\Authorization\Voter;

use Tagcade\Model\Core\BlacklistInterface;
use Tagcade\Model\User\UserEntityInterface;

class BlacklistVoter extends EntityVoterAbstract
{
    public function supportsClass($class)
    {
        $supportedClass = BlacklistInterface::class;

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param BlacklistInterface $blacklist
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isPublisherActionAllowed($blacklist, UserEntityInterface $user, $action)
    {
        return $user->getId() == $blacklist->getPublisherId();
    }

    /**
     * @param BlacklistInterface $blacklist
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isSubPublisherActionAllowed($blacklist, UserEntityInterface $user, $action)
    {
        return true;
    }
}