<?php

namespace Tagcade\Repository;

use Doctrine\Common\Persistence\ObjectRepository;
use Tagcade\Model\User\Role\PublisherInterface;

interface SiteRepositoryInterface extends ObjectRepository
{
    /**
     * @param PublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function getSitesForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);
}