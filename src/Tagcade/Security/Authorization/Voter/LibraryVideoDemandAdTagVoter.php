<?php

namespace Tagcade\Security\Authorization\Voter;

use Tagcade\Model\Core\LibraryVideoDemandAdTagInterface;
use Tagcade\Model\User\UserEntityInterface;

class LibraryVideoDemandAdTagVoter extends EntityVoterAbstract
{
    public function supportsClass($class)
    {
        $supportedClass = LibraryVideoDemandAdTagInterface::class;

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param LibraryVideoDemandAdTagInterface $libraryVideoDemandAdTag
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isPublisherActionAllowed($libraryVideoDemandAdTag, UserEntityInterface $user, $action)
    {
        return $user->getId() == $libraryVideoDemandAdTag->getPublisherId();
    }

    /**
     * @param LibraryVideoDemandAdTagInterface $libraryVideoDemandAdTag
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isSubPublisherActionAllowed($libraryVideoDemandAdTag, UserEntityInterface $user, $action)
    {
        // not allowed
        return false;
    }
}