<?php

namespace Tagcade\Security\Authorization\Voter;

use Tagcade\Model\Core\LibraryAdTagInterface;
use Tagcade\Model\User\UserEntityInterface;

class LibraryAdTagVoter extends EntityVoterAbstract
{
    public function supportsClass($class)
    {
        $supportedClass = LibraryAdTagInterface::class;

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param LibraryAdTagInterface $libraryAdTag
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isPublisherActionAllowed($libraryAdTag, UserEntityInterface $user, $action)
    {
        return $user->getId() == $libraryAdTag->getAdNetwork()->getPublisherId();
    }
}