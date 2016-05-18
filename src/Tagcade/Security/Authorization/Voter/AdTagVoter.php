<?php

namespace Tagcade\Security\Authorization\Voter;

use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;
use Tagcade\Model\User\UserEntityInterface;

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
        $subPublisher = $adTag->getAdSlot()->getSite()->getSubPublisher();

        if (!$subPublisher instanceof SubPublisherInterface) {
            // this ad tag belongs to a site which does not belong to any sub publisher
            return false;
        }

        // check subPublisherId
        $isSubPublisherAllowed = ($user->getId() === $subPublisher->getId()) &&($subPublisher->isDemandSourceTransparency());

        return $isSubPublisherAllowed && strcasecmp($action, 'view') === 0;
    }
}