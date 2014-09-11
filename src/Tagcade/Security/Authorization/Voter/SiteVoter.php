<?php

namespace Tagcade\Security\Authorization\Voter;

use Tagcade\Model\User\UserEntityInterface;
use Tagcade\Model\Core\SiteInterface;

class SiteVoter extends EntityVoterAbstract
{
    public function supportsClass($class)
    {
        $supportedClass = SiteInterface::class;

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param SiteInterface $site
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isPublisherActionAllowed($site, UserEntityInterface $user, $action)
    {
        return $user->getId() == $site->getPublisherId();
    }
}