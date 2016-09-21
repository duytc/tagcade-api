<?php

namespace Tagcade\Security\Authorization\Voter;

use Tagcade\Model\Core\VideoWaterfallTagItemInterface;
use Tagcade\Model\User\UserEntityInterface;

class VideoWaterfallTagItemVoter extends EntityVoterAbstract
{
    public function supportsClass($class)
    {
        $supportedClass = VideoWaterfallTagItemInterface::class;

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param VideoWaterfallTagItemInterface $videoWaterfallTagItem
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isPublisherActionAllowed($videoWaterfallTagItem, UserEntityInterface $user, $action)
    {
        return $user->getId() == $videoWaterfallTagItem->getVideoWaterfallTag()->getPublisher()->getId();
    }

    /**
     * @param VideoWaterfallTagItemInterface $videoWaterfallTagItem
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isSubPublisherActionAllowed($videoWaterfallTagItem, UserEntityInterface $user, $action)
    {
        // not allowed
        return false;
    }
}