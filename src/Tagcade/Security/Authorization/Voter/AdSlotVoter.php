<?php

namespace Tagcade\Security\Authorization\Voter;

use Tagcade\Model\Core\BaseAdSlotInterface;
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
        if (count($adSlot->getSite()->getSubPublisherSites()) < 1) {
            // this ad slot belongs to a site which does not allow access to any sub publisher
            return false;
        }

        // check subPublisherId
        $isSubPublisherAllowed = false;

        foreach ($adSlot->getSite()->getSubPublishers() as $subPublisher) {
            if ($user->getId() === $subPublisher->getId()) {
                $isSubPublisherAllowed = true;
                break;
            }
        }

        return $isSubPublisherAllowed && strcasecmp($action, 'view') === 0;
    }
}