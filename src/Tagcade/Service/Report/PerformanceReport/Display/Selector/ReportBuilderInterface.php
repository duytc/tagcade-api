<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector;

use Tagcade\Domain\DTO\Report\PerformanceReport\Display\Group\ReportGroup;
use Tagcade\Domain\DTO\Report\PerformanceReport\Display\ReportCollection;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdSlotInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface ReportBuilderInterface
{
    /**
     * @param array $params
     * @return ReportGroup|ReportGroup[]|ReportCollection|ReportCollection[]
     */
    public function getPlatformReport(array $params = []);

    /**
     * @param array $params
     * @return ReportGroup|ReportGroup[]|ReportCollection|ReportCollection[]
     */
    public function getAllPublishersReport(array $params = []);

    /**
     * @param PublisherInterface $publisher
     * @param array $params
     * @return ReportGroup|ReportGroup[]|ReportCollection|ReportCollection[]
     */
    public function getPublisherReport(PublisherInterface $publisher, array $params = []);

    /**
     * @param PublisherInterface $publisher
     * @param array $params
     * @return ReportGroup|ReportGroup[]|ReportCollection|ReportCollection[]
     */
    public function getPublisherAdNetworksReport(PublisherInterface $publisher, array $params = []);

    /**
     * @param AdNetworkInterface $adNetwork
     * @param array $params
     * @return ReportGroup|ReportGroup[]|ReportCollection|ReportCollection[]
     */
    public function getAdNetworkReport(AdNetworkInterface $adNetwork, array $params = []);

    /**
     * @param AdNetworkInterface $adNetwork
     * @param array $params
     * @return ReportGroup|ReportGroup[]|ReportCollection|ReportCollection[]
     */
    public function getAdnetworkSitesReport(AdNetworkInterface $adNetwork, array $params = []);

    /**
     * @param AdNetworkInterface $adNetwork
     * @param SiteInterface $site
     * @param array $params
     * @return ReportGroup|ReportGroup[]|ReportCollection|ReportCollection[]
     */
    public function getAdNetworkSiteReport(AdNetworkInterface $adNetwork, SiteInterface $site, array $params = []);

    /**
     * @param array $params
     * @return ReportGroup|ReportGroup[]|ReportCollection|ReportCollection[]
     */
    public function getAllSitesReport(array $params = []);

    /**
     * @param PublisherInterface $publisher
     * @param array $params
     * @return ReportGroup|ReportGroup[]|ReportCollection|ReportCollection[]
     */
    public function getPublisherSitesReport(PublisherInterface $publisher, array $params = []);

    /**
     * @param SiteInterface $site
     * @param array $params
     * @return ReportGroup|ReportGroup[]|ReportCollection|ReportCollection[]
     */
    public function getSiteReport(SiteInterface $site, array $params = []);

    /**
     * @param SiteInterface $site
     * @param array $params
     * @return ReportGroup|ReportGroup[]|ReportCollection|ReportCollection[]
     */
    public function getSiteAdSlotsReport(SiteInterface $site, array $params = []);

    /**
     * @param AdSlotInterface $adSlot
     * @param array $params
     * @return ReportGroup|ReportGroup[]|ReportCollection|ReportCollection[]
     */
    public function getAdSlotReport(AdSlotInterface $adSlot, array $params = []);

    /**
     * @param AdSlotInterface $adSlot
     * @param array $params
     * @return ReportGroup|ReportGroup[]|ReportCollection|ReportCollection[]
     */
    public function getAdSlotAdTagsReport(AdSlotInterface $adSlot, array $params = []);

    /**
     * @param AdTagInterface $adTag
     * @param array $params
     * @return ReportGroup|ReportGroup[]|ReportCollection|ReportCollection[]
     */
    public function getAdTagReport(AdTagInterface $adTag, array $params = []);
}