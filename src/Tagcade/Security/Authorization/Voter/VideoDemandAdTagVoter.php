<?php

namespace Tagcade\Security\Authorization\Voter;

use Tagcade\Model\Core\VideoDemandAdTagInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;
use Tagcade\Model\User\UserEntityInterface;

class VideoDemandAdTagVoter extends EntityVoterAbstract
{
    public function supportsClass($class)
    {
        $supportedClass = VideoDemandAdTagInterface::class;

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param VideoDemandAdTagInterface $videoDemandAdTag
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isPublisherActionAllowed($videoDemandAdTag, UserEntityInterface $user, $action)
    {
        return $user->getId() == $videoDemandAdTag->getVideoDemandPartner()->getPublisher()->getId();
    }

    /**
     * @param VideoDemandAdTagInterface $videoDemandAdTag
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isSubPublisherActionAllowed($videoDemandAdTag, UserEntityInterface $user, $action)
    {
        /**
         * @var SubPublisherInterface $user
         */
        if($action == self::VIEW) {
            return true;
        }

        // not allowed
        return false;
    }
}