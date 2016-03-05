<?php

namespace Tagcade\Security\Authorization\Voter;

use Doctrine\Common\Collections\Collection;
use Tagcade\Model\Core\ExchangeInterface;
use Tagcade\Model\Core\PublisherExchangeInterface;
use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\UserEntityInterface;

class ExchangeVoter extends EntityVoterAbstract
{
    public function supportsClass($class)
    {
        $supportedClass = ExchangeInterface::class;

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param ExchangeInterface $exchange
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isPublisherActionAllowed($exchange, UserEntityInterface $user, $action)
    {
        /** @var PublisherExchangeInterface[]|Collection $publisherExchanges */
        $publisherExchanges = $exchange->getPublisherExchanges();

        if (count($publisherExchanges) < 1) {
            return true;
        }

        if ($publisherExchanges instanceof Collection) {
            $publisherExchanges = $publisherExchanges->toArray();
        }

        return $user->getId() == $publisherExchanges[0]->getPublisher()->getId();
    }

    /**
     * Checks to see if a sub publisher has permission to perform an action
     *
     * @param ModelInterface $entity
     * @param UserEntityInterface $user
     * @param $action
     * @return bool
     */
    protected function isSubPublisherActionAllowed($entity, UserEntityInterface $user, $action)
    {
        return false;
    }
}