<?php

namespace Tagcade\Security\Authorization\Voter;

use Tagcade\Model\Core\ChannelInterface;
use Tagcade\Model\User\UserEntityInterface;

class ChannelVoter extends EntityVoterAbstract
{
    public function supportsClass($class)
    {
        $supportedClass = ChannelInterface::class;

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param ChannelInterface $channel
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isPublisherActionAllowed($channel, UserEntityInterface $user, $action)
    {
        return $user->getId() == $channel->getPublisherId();
    }
}