<?php

namespace Tagcade\Security\Authorization\Voter;

use Tagcade\Model\Core\VideoPublisherInterface;
use Tagcade\Model\User\UserEntityInterface;

class VideoPublisherVoter extends EntityVoterAbstract
{
    /**
     * @inheritdoc
     */
    public function supportsClass($class)
    {
        $supportedClass = VideoPublisherInterface::class;

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param VideoPublisherInterface $videoPublisher
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isPublisherActionAllowed($videoPublisher, UserEntityInterface $user, $action)
    {
        return $user->getId() == $videoPublisher->getPublisher()->getId();
    }

    /**
     * @param VideoPublisherInterface $videoPublisher
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isSubPublisherActionAllowed($videoPublisher, UserEntityInterface $user, $action)
    {
        // not allowed
        return false;
    }
}