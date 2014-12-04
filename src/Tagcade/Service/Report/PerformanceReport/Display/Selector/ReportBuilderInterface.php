<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector;

use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\ReportResultInterface;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdSlotInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface ReportBuilderInterface
{
    /**
     * @param Params $params
     * @return ReportResultInterface|false
     */
    public function getPlatformReport(Params $params);

    /**
     * @param Params $params
     * @return ReportResultInterface|false
     */
    public function getAllPublishersReport(Params $params);

    /**
     * @param PublisherInterface $publisher
     * @param Params $params
     * @return ReportResultInterface|false
     */
    public function getPublisherReport(PublisherInterface $publisher, Params $params);

    /**
     * @param PublisherInterface $publisher
     * @param Params $params
     * @return ReportResultInterface|false
     */
    public function getPublisherAdNetworksReport(PublisherInterface $publisher, Params $params);

    /**
     * @param PublisherInterface $publisher
     * @param Params $params
     * @return ReportResultInterface|false
     */
    public function getPublisherAdSlotsReport(PublisherInterface $publisher, Params $params);

    /**
     * @param AdNetworkInterface $adNetwork
     * @param Params $params
     * @return ReportResultInterface|false
     */
    public function getAdNetworkReport(AdNetworkInterface $adNetwork, Params $params);

    /**
     * @param AdNetworkInterface $adNetwork
     * @param Params $params
     * @return ReportResultInterface|false
     */
    public function getAdnetworkSitesReport(AdNetworkInterface $adNetwork, Params $params);

    /**
     * @param AdNetworkInterface $adNetwork
     * @param SiteInterface $site
     * @param Params $params
     * @return ReportResultInterface|false
     */
    public function getAdNetworkSiteReport(AdNetworkInterface $adNetwork, SiteInterface $site, Params $params);

    /**
     * @param Params $params
     * @return ReportResultInterface|false
     */
    public function getAllSitesReport(Params $params);

    /**
     * @param PublisherInterface $publisher
     * @param Params $params
     * @return ReportResultInterface|false
     */
    public function getPublisherSitesReport(PublisherInterface $publisher, Params $params);

    /**
     * @param SiteInterface $site
     * @param Params $params
     * @return ReportResultInterface|false
     */
    public function getSiteReport(SiteInterface $site, Params $params);

    /**
     * @param SiteInterface $site
     * @param Params $params
     * @return ReportResultInterface|false
     */
    public function getSiteAdSlotsReport(SiteInterface $site, Params $params);

    /**
     * @param AdSlotInterface $adSlot
     * @param Params $params
     * @return ReportResultInterface|false
     */
    public function getAdSlotReport(AdSlotInterface $adSlot, Params $params);

    /**
     * @param AdSlotInterface $adSlot
     * @param Params $params
     * @return ReportResultInterface|false
     */
    public function getAdSlotAdTagsReport(AdSlotInterface $adSlot, Params $params);

    /**
     * @param AdTagInterface $adTag
     * @param Params $params
     * @return ReportResultInterface|false
     */
    public function getAdTagReport(AdTagInterface $adTag, Params $params);
}