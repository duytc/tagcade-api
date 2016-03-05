<?php

namespace Tagcade\Security\Authorization\Voter;

use Tagcade\Model\Core\DynamicAdSlotInterface;
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
     * @param DynamicAdSlotInterface $adSlot
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isSubPublisherActionAllowed($adSlot, UserEntityInterface $user, $action)
    {
        // check subPublisherId
        $isAllowedSubPublisher = false;

        foreach ($adSlot->getSite()->getSubPublishers() as $subPublisher) {
            if ($user->getId() === $subPublisher->getId()) {
                $isAllowedSubPublisher = true;
                break;
            }
        }

        if (!$isAllowedSubPublisher) {
            return false;
        }

        // check access grant for config subPublisher-site
        if (count($adSlot->getSite()->getSubPublisherSites()) < 1) {
            // no config => decline
            return false;
        }

        $access = $adSlot->getSite()->getSubPublisherSites()[0]->getAccess();

        if (!$this->allowsAccess($access, $action)) {
            return false;
        }

        return true;
    }
}