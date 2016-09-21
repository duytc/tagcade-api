<?php

namespace Tagcade\Security\Authorization\Voter;

use Tagcade\Model\Core\VideoDemandPartnerInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;
use Tagcade\Model\User\UserEntityInterface;

class VideoDemandPartnerVoter extends EntityVoterAbstract
{
    public function supportsClass($class)
    {
        $supportedClass = VideoDemandPartnerInterface::class;

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param VideoDemandPartnerInterface $videoDemandPartner
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isPublisherActionAllowed($videoDemandPartner, UserEntityInterface $user, $action)
    {
        return $user->getId() == $videoDemandPartner->getPublisher()->getId();
    }

    /**
     * @param VideoDemandPartnerInterface $videoDemandPartner
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isSubPublisherActionAllowed($videoDemandPartner, UserEntityInterface $user, $action)
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