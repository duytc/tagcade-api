<?php

namespace Tagcade\DomainManager;

use Tagcade\Model\User\Role\PublisherInterface;

interface BillingConfigurationManagerInterface extends ManagerInterface
{
    /**
     * @param PublisherInterface $publisher
     * @return mixed
     */
    public function getAllConfigurationForPublisher(PublisherInterface $publisher);
}