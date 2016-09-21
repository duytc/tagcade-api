<?php

namespace Tagcade\Security\Authorization\Voter;

use Tagcade\Model\Core\WhiteListInterface;
use Tagcade\Model\User\UserEntityInterface;

class WhiteListVoter extends EntityVoterAbstract
{
    public function supportsClass($class)
    {
        $supportedClass = WhiteListInterface::class;

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param WhiteListInterface $whiteList
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isPublisherActionAllowed($whiteList, UserEntityInterface $user, $action)
    {
        return $user->getId() == $whiteList->getPublisherId();
    }

    /**
     * @param WhiteListInterface $whiteList
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isSubPublisherActionAllowed($whiteList, UserEntityInterface $user, $action)
    {
        return true;
    }
}