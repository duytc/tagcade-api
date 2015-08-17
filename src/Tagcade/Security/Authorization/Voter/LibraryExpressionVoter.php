<?php

namespace Tagcade\Security\Authorization\Voter;

use Tagcade\Model\Core\LibraryAdTagInterface;
use Tagcade\Model\Core\LibraryExpressionInterface;
use Tagcade\Model\User\UserEntityInterface;

class LibraryExpressionVoter extends EntityVoterAbstract
{
    public function supportsClass($class)
    {
        $supportedClass = LibraryExpressionInterface::class;

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
        return true;
    }
}