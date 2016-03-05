<?php

namespace Tagcade\Security\Authorization\Voter;

use Tagcade\Model\User\UserEntityInterface;
use Tagcade\Model\Core\AdNetworkInterface;

class AdNetworkVoter extends EntityVoterAbstract
{
    public function supportsClass($class)
    {
        $supportedClass = AdNetworkInterface::class;

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param AdNetworkInterface $adNetwork
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isPublisherActionAllowed($adNetwork, UserEntityInterface $user, $action)
    {
        return $user->getId() == $adNetwork->getPublisherId();
    }

    /**
     * @param AdNetworkInterface $adNetwork
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isSubPublisherActionAllowed($adNetwork, UserEntityInterface $user, $action)
    {
        // not allowed
        return false;
    }
}