<?php

namespace Tagcade\Repository\Core;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\QueryBuilder;
use Tagcade\Model\Core\AdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface DynamicAdSlotRepositoryInterface extends ObjectRepository
{
    /**
     * @param SiteInterface $site
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function getDynamicAdSlotsForSite(SiteInterface $site, $limit = null, $offset = null);

    /**
     * @param PublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function getDynamicAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    /**
     * @param PublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return QueryBuilder
     */
    public function getDynamicAdSlotsForPublisherQuery(PublisherInterface $publisher, $limit = null, $offset = null);

}