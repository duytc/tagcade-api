<?php

namespace Tagcade\Security\Authorization\Voter;

use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;
use Tagcade\Model\User\UserEntityInterface;

class AdSlotVoter extends EntityVoterAbstract
{
    public function supportsClass($class)
    {
        $supportedClass = BaseAdSlotInterface::class;

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param BaseAdSlotInterface $adSlot
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isPublisherActionAllowed($adSlot, UserEntityInterface $user, $action)
    {
        return $user->getId() == $adSlot->getSite()->getPublisherId();
    }

    /**
     * @param BaseAdSlotInterface $adSlot
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isSubPublisherActionAllowed($adSlot, UserEntityInterface $user, $action)
    {
        /**
         * @var SubPublisherInterface $user
         */
        if($action == self::VIEW ) {
            return true;
        }

        // not allowed
        return false;
    }
}