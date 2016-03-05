<?php

namespace Tagcade\Security\Authorization\Voter;

use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Model\User\UserEntityInterface;
use Tagcade\Model\Core\AdTagInterface;

class AdTagVoter extends EntityVoterAbstract
{
    public function supportsClass($class)
    {
        $supportedClass = AdTagInterface::class;

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param AdTagInterface $adTag
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isPublisherActionAllowed($adTag, UserEntityInterface $user, $action)
    {
        $adSlot = $adTag->getAdSlot();
        $publisherId = $adSlot->getSite()->getPublisherId();

        return $user->getId() == $publisherId;
    }

    /**
     * @param AdTagInterface $adTag
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isSubPublisherActionAllowed($adTag, UserEntityInterface $user, $action)
    {
        $adSlot = $adTag->getAdSlot();

        if (count($adSlot->getSite()->getSubPublisherSites()) < 1) {
            // this ad tag belongs to a site does not allow access to any sub publisher
            return false;
        }

        // check subPublisherId
        $isSubPublisherAllowed = false;

        $subPublishers = $adSlot->getSite()->getSubPublishers();

        foreach ($subPublishers as $subPublisher) {
            if ($user->getId() === $subPublisher->getId()) {
                $isSubPublisherAllowed = true;
                break;
            }
        }

        return $isSubPublisherAllowed && strcasecmp($action, 'view') === 0;
    }
}