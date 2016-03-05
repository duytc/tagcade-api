<?php

namespace Tagcade\Security\Authorization\Voter;

use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;
use Tagcade\Model\User\UserEntityInterface;

class PublisherVoter extends EntityVoterAbstract
{
    public function supportsClass($class)
    {
        $supportedClass = PublisherInterface::class;

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param PublisherInterface $account
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isPublisherActionAllowed($account, UserEntityInterface $user, $action)
    {
        $publisherId = $account instanceof SubPublisherInterface ? $account->getPublisher()->getId() : $account->getId();

        return $user->getId() == $publisherId;
    }

    /**
     * @param PublisherInterface $account
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isSubPublisherActionAllowed($account, UserEntityInterface $user, $action)
    {
        if ($account instanceof SubPublisherInterface) {
            return $user->getId() == $account->getId();
        }

        // not allowed SubPublisher does an action on account as Publisher
        return false;
    }
}