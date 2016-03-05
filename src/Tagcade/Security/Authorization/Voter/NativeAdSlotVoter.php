<?php

namespace Tagcade\Security\Authorization\Voter;

use Tagcade\Model\Core\NativeAdSlotInterface;
use Tagcade\Model\User\UserEntityInterface;

class NativeAdSlotVoter extends EntityVoterAbstract
{
    public function supportsClass($class)
    {
        $supportedClass = NativeAdSlotInterface::class;

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param NativeAdSlotInterface $nativeAdSlot
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isPublisherActionAllowed($nativeAdSlot, UserEntityInterface $user, $action)
    {
        return $user->getId() == $nativeAdSlot->getSite()->getPublisherId();
    }

    /**
     * @param NativeAdSlotInterface $nativeAdSlot
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isSubPublisherActionAllowed($nativeAdSlot, UserEntityInterface $user, $action)
    {
        // check subPublisherId
        $isAllowedSubPublisher = false;

        foreach ($nativeAdSlot->getSite()->getSubPublishers() as $subPublisher) {
            if ($user->getId() === $subPublisher->getId()) {
                $isAllowedSubPublisher = true;
                break;
            }
        }

        if (!$isAllowedSubPublisher) {
            return false;
        }

        // check access grant for config subPublisher-site
        if (count($nativeAdSlot->getSite()->getSubPublisherSites()) < 1) {
            // no config => decline
            return false;
        }

        $access = $nativeAdSlot->getSite()->getSubPublisherSites()[0]->getAccess();

        if (!$this->allowsAccess($access, $action)) {
            return false;
        }

        return true;
    }
}