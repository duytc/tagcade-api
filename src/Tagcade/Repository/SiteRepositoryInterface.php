<?php

namespace Tagcade\Repository;

use Tagcade\Model\User\Role\PublisherInterface;

interface SiteRepositoryInterface
{
    /**
     * @param PublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function getSitesForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);
}