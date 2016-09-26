<?php

namespace Tagcade\DomainManager;

use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface BlacklistManagerInterface extends ManagerInterface
{
    /**
     * @param PublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return SiteInterface[]
     */
    public function getBlacklistsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);
}