<?php


namespace Tagcade\Service;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;
use Tagcade\Exception\LogicException;
use Tagcade\Model\User\Role\PublisherInterface;


trait StringUtilTrait
{
    /**
     * Check if the given string is a valid domain
     *
     * @param $domain
     * @return bool
     */
    protected function validateDomain($domain)
    {
        return preg_match('/^(?:[-A-Za-z0-9]+\.)+[A-Za-z]{2,6}$/', $domain) > 0;
    }

    /**
     * Generate UUID for a given publisher
     *
     * @param PublisherInterface $publisher
     * @return string
     */
    protected function generateUuid(PublisherInterface $publisher)
    {
        try {
            $uuid5 = Uuid::uuid5(Uuid::NAMESPACE_DNS, $publisher->getEmail());
            return $uuid5->toString();

        } catch(UnsatisfiedDependencyException $e) {
            throw new LogicException($e->getMessage());
        }
    }
}