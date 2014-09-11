<?php

namespace Tagcade\Repository\Core;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\QueryBuilder;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface AdSlotRepositoryInterface extends ObjectRepository
{
    /**
     * @param SiteInterface $site
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function getAdSlotsForSite(SiteInterface $site, $limit = null, $offset = null);

    /**
     * @param PublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function getAdSlotsForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    /**
     * @param PublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return QueryBuilder
     */
    public function getAdSlotsForPublisherQuery(PublisherInterface $publisher, $limit = null, $offset = null);
}