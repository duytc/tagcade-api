<?php

namespace Tagcade\Repository\Core;

use Doctrine\Common\Persistence\ObjectRepository;
use Tagcade\Model\User\Role\PublisherInterface;

interface BillingConfigurationRepositoryInterface extends ObjectRepository
{
    /**
     * @param PublisherInterface $publisher
     * @return array
     */
    public function getAllConfigurationForPublisher(PublisherInterface $publisher);

    /**
     * @param PublisherInterface $publisher
     * @param $module
     * @return mixed
     */
    public function getConfigurationForModule(PublisherInterface $publisher, $module);
}