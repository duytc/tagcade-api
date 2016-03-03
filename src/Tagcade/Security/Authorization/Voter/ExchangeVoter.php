<?php

namespace Tagcade\Security\Authorization\Voter;

use Tagcade\Model\Core\ExchangeInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\UserEntityInterface;
use Tagcade\Model\Core\AdNetworkInterface;

class ExchangeVoter extends EntityVoterAbstract
{
    public function supportsClass($class)
    {
        $supportedClass = ExchangeInterface::class;

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param AdNetworkInterface $exchange
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isPublisherActionAllowed($exchange, UserEntityInterface $user, $action)
    {
        if ($action === self::VIEW) {
            return true;
        }

        return false;
    }
}