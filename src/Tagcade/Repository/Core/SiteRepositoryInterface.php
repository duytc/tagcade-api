<?php

namespace Tagcade\Repository\Core;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\QueryBuilder;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\Platform\SiteInterface;

interface SiteRepositoryInterface extends ObjectRepository
{
    /**
     * @param PublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function getSitesForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    /**
     * @param UserRoleInterface $user
     * @param null $limit
     * @param null $offset
     * @return array
     */
    public function getAutoCreatedSites(UserRoleInterface $user, $limit = null, $offset = null);

    /**
     * @param UserRoleInterface $user
     * @param null $limit
     * @param null $offset
     * @return array
     */
    public function getManualCreatedSites(UserRoleInterface $user, $limit = null, $offset = null);

    /**
     * @param PublisherInterface $publisher
     * @param int|null $limit
     * @param int|null $offset
     * @return QueryBuilder
     */
    public function getSitesForPublisherQuery(PublisherInterface $publisher, $limit = null, $offset = null);

    public function getSitesThatHaveAdTagsBelongingToAdNetwork(AdNetworkInterface $adNetwork, $limit = null, $offset = null);

    public function getSiteIdsThatHaveAdTagsBelongingToAdNetwork(AdNetworkInterface $adNetwork, $limit = null, $offset = null);

    /**
     * * Get all sites that have configured or not configured for source report by publisher
     *
     * @param PublisherInterface $publisher
     * @param bool $hasSourceReportConfig
     * @return array
     */
    public function getSitesThatHastConfigSourceReportForPublisher(PublisherInterface $publisher, $hasSourceReportConfig = true);

    public function getSitesThatEnableSourceReportForPublisher(PublisherInterface $publisher, $enableSourceReport = true);

    public function getAllSitesThatEnableSourceReport($enableSourceReport = true);

    /**
     * get all Sites which have no Ad Slot references to a library Ad Slot
     *
     * @param $slotLibrary
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function getSitesUnreferencedToLibraryAdSlot(BaseLibraryAdSlotInterface $slotLibrary, $limit = null, $offset = null);

    /**
     * Check if a site with the given domain already existed
     *
     * @param PublisherInterface $publisher
     * @param $domain
     * @param bool $useHash using hash to find site by domain and publisher. The hash is md5 publisher id and its domain
     * @return null|SiteInterface[]
     */
    public function getSitesByDomainAndPublisher(PublisherInterface $publisher, $domain, $useHash = false);
}