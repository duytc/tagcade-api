<?php

namespace Tagcade\Security\Authorization\Voter;

use Tagcade\Model\Core\VideoWaterfallTagInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;
use Tagcade\Model\User\UserEntityInterface;

class VideoWaterfallTagVoter extends EntityVoterAbstract
{
    public function supportsClass($class)
    {
        $supportedClass = VideoWaterfallTagInterface::class;

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param VideoWaterfallTagInterface $videoWaterfallTag
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isPublisherActionAllowed($videoWaterfallTag, UserEntityInterface $user, $action)
    {
        return $user->getId() == $videoWaterfallTag->getPublisher()->getId();
    }

    /**
     * @param VideoWaterfallTagInterface $videoWaterfallTag
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isSubPublisherActionAllowed($videoWaterfallTag, UserEntityInterface $user, $action)
    {
        /**
         * @var SubPublisherInterface $user
         */
        if ($action == self::VIEW) {
            return true;
        }

        // not allowed
        return false;
    }
}