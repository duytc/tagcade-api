<?php

namespace Tagcade\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Exception\InvalidArgumentException;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

use Tagcade\Model\User\UserEntityInterface;
use Tagcade\Model\SiteInterface;

class SiteVoter implements VoterInterface
{
    const VIEW = 'view';
    const EDIT = 'edit';

    public function supportsAttribute($attribute)
    {
        return in_array($attribute, array(
            self::VIEW,
            self::EDIT,
        ));
    }

    public function supportsClass($class)
    {
        $supportedClass = SiteInterface::class;

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param TokenInterface $token
     * @param SiteInterface $site
     * @param array $attributes
     * @return int
     * @throws InvalidArgumentException
     */
    public function vote(TokenInterface $token, $site, array $attributes)
    {
        if (!$this->supportsClass(get_class($site))) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        if(1 !== count($attributes)) {
            throw new InvalidArgumentException('Only one attribute is allowed');
        }

        $attribute = $attributes[0];

        if (!$this->supportsAttribute($attribute)) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        /** @var UserEntityInterface $user */
        $user = $token->getUser();

        if (!$user instanceof UserEntityInterface) {
            return VoterInterface::ACCESS_DENIED;
        }

        // process is currently the same for voting on VIEW or EDIT
        // allow admins and only allow publishers who own the site entity

        if ($user->hasRole('ROLE_ADMIN')) {
            return VoterInterface::ACCESS_GRANTED;
        }

        if ($user->hasRole('ROLE_PUBLISHER') && $user->getId() == $site->getPublisherId()) {
            return VoterInterface::ACCESS_GRANTED;
        }

        return VoterInterface::ACCESS_DENIED;
    }
}