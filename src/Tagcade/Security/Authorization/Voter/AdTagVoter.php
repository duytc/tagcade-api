<?php

namespace Tagcade\Security\Authorization\Voter;

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
        return $user->getId() == $adTag->getAdSlot()->getSite()->getPublisherId();
    }
}