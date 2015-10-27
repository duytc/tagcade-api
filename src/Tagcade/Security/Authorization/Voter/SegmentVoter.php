<?php

namespace Tagcade\Security\Authorization\Voter;

use Tagcade\Model\Core\SegmentInterface;
use Tagcade\Model\User\UserEntityInterface;

class SegmentVoter extends EntityVoterAbstract
{
    public function supportsClass($class)
    {
        $supportedClass = SegmentInterface::class;

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param SegmentInterface $segment
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isPublisherActionAllowed($segment, UserEntityInterface $user, $action)
    {
        return $user->getId() == $segment->getPublisherId();
    }
}