<?php

namespace Tagcade\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Exception\InvalidArgumentException;
use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\UserEntityInterface;

abstract class EntityVoterAbstract implements VoterInterface
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

    /**
     * @param TokenInterface $token
     * @param ModelInterface $entity
     * @param array $attributes
     * @return int
     * @throws InvalidArgumentException
     */
    public function vote(TokenInterface $token, $entity, array $attributes)
    {
        if (!$this->supportsClass(get_class($entity))) {
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

        if ($user->hasRole('ROLE_PUBLISHER') && $this->isPublisherActionAllowed($entity, $user, $attribute)) {
            return VoterInterface::ACCESS_GRANTED;
        }

        if ($user->hasRole('ROLE_SUB_PUBLISHER') && $this->isSubPublisherActionAllowed($entity, $user, $attribute)) {
            return VoterInterface::ACCESS_GRANTED;
        }

        return VoterInterface::ACCESS_DENIED;
    }

    /**
     * Checks to see if a publisher has permission to perform an action
     *
     * @param ModelInterface $entity
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    abstract protected function isPublisherActionAllowed($entity, UserEntityInterface $user, $action);

    /**
     * Checks to see if a sub publisher has permission to perform an action
     *
     * @param ModelInterface $entity
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    abstract protected function isSubPublisherActionAllowed($entity, UserEntityInterface $user, $action);
}