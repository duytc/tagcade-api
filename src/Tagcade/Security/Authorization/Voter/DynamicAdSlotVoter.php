<?php

namespace Tagcade\Security\Authorization\Voter;

use Tagcade\Model\Core\DynamicAdSlotInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;
use Tagcade\Model\User\UserEntityInterface;

class DynamicAdSlotVoter extends EntityVoterAbstract
{
    public function supportsClass($class)
    {
        $supportedClass = DynamicAdSlotInterface::class;

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param DynamicAdSlotInterface $adSlot
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isPublisherActionAllowed($adSlot, UserEntityInterface $user, $action)
    {
        return $user->getId() == $adSlot->getSite()->getPublisherId();
    }

    /**
     * @param DynamicAdSlotInterface $dynamicAdSlot
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isSubPublisherActionAllowed($dynamicAdSlot, UserEntityInterface $user, $action)
    {
        $subPublisher = $dynamicAdSlot->getSite()->getSubPublisher();

        if (!$subPublisher instanceof SubPublisherInterface) {
            // this ad slot belongs to a site which does not belong to any sub publisher
            return false;
        }

        // check subPublisherId
        $isSubPublisherAllowed = $user->getId() === $subPublisher->getId();

        return $isSubPublisherAllowed && strcasecmp($action, 'view') === 0;
    }
}