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
     * @param Params $params
     * @return ReportGroup|ReportGroup[]|ReportCollection|ReportCollection[]
     */
    public function getPlatformReport(Params $params);

    /**
     * @param Params $params
     * @return ReportGroup|ReportGroup[]|ReportCollection|ReportCollection[]
     */
    public function getAllPublishersReport(Params $params);

    /**
     * @param PublisherInterface $publisher
     * @param Params $params
     * @return ReportGroup|ReportGroup[]|ReportCollection|ReportCollection[]
     */
    public function getPublisherReport(PublisherInterface $publisher, Params $params);

    /**
     * @param PublisherInterface $publisher
     * @param Params $params
     * @return ReportGroup|ReportGroup[]|ReportCollection|ReportCollection[]
     */
    public function getPublisherAdNetworksReport(PublisherInterface $publisher, Params $params);

    /**
     * @param PublisherInterface $publisher
     * @param Params $params
     * @return ReportGroup|ReportGroup[]|ReportCollection|ReportCollection[]
     */
    public function getPublisherAdSlotsReport(PublisherInterface $publisher, Params $params);

    /**
     * @param AdNetworkInterface $adNetwork
     * @param Params $params
     * @return ReportGroup|ReportGroup[]|ReportCollection|ReportCollection[]
     */
    public function getAdNetworkReport(AdNetworkInterface $adNetwork, Params $params);

    /**
     * @param AdNetworkInterface $adNetwork
     * @param Params $params
     * @return ReportGroup|ReportGroup[]|ReportCollection|ReportCollection[]
     */
    public function getAdnetworkSitesReport(AdNetworkInterface $adNetwork, Params $params);

    /**
     * @param AdNetworkInterface $adNetwork
     * @param SiteInterface $site
     * @param Params $params
     * @return ReportGroup|ReportGroup[]|ReportCollection|ReportCollection[]
     */
    public function getAdNetworkSiteReport(AdNetworkInterface $adNetwork, SiteInterface $site, Params $params);

    /**
     * @param Params $params
     * @return ReportGroup|ReportGroup[]|ReportCollection|ReportCollection[]
     */
    public function getAllSitesReport(Params $params);

    /**
     * @param PublisherInterface $publisher
     * @param Params $params
     * @return ReportGroup|ReportGroup[]|ReportCollection|ReportCollection[]
     */
    public function getPublisherSitesReport(PublisherInterface $publisher, Params $params);

    /**
     * @param SiteInterface $site
     * @param Params $params
     * @return ReportGroup|ReportGroup[]|ReportCollection|ReportCollection[]
     */
    public function getSiteReport(SiteInterface $site, Params $params);

    /**
     * @param SiteInterface $site
     * @param Params $params
     * @return ReportGroup|ReportGroup[]|ReportCollection|ReportCollection[]
     */
    public function getSiteAdSlotsReport(SiteInterface $site, Params $params);

    /**
     * @param AdSlotInterface $adSlot
     * @param Params $params
     * @return ReportGroup|ReportGroup[]|ReportCollection|ReportCollection[]
     */
    public function getAdSlotReport(AdSlotInterface $adSlot, Params $params);

    /**
     * @param AdSlotInterface $adSlot
     * @param Params $params
     * @return ReportGroup|ReportGroup[]|ReportCollection|ReportCollection[]
     */
    public function getAdSlotAdTagsReport(AdSlotInterface $adSlot, Params $params);

    /**
     * @param AdTagInterface $adTag
     * @param Params $params
     * @return ReportGroup|ReportGroup[]|ReportCollection|ReportCollection[]
     */
    public function getAdTagReport(AdTagInterface $adTag, Params $params);
}