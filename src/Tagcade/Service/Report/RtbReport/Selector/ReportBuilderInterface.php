<?php

namespace Tagcade\Service\Report\RtbReport\Selector;

use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\Report\RtbReport\Selector\Result\ReportResultInterface;

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
    public function getPublisherDSPsReport(PublisherInterface $publisher, Params $params);

    /**
     * @param AdNetworkInterface $adNetwork
     * @param Params $params
     * @return ReportResultInterface|false
     */
    public function getDSPReport(AdNetworkInterface $adNetwork, Params $params);

    /**
     * @param AdNetworkInterface $adNetwork
     * @param Params $params
     * @return ReportResultInterface|false
     */
    public function getDSPSitesReport(AdNetworkInterface $adNetwork, Params $params);

    /**
     * @param AdNetworkInterface $adNetwork
     * @param SiteInterface $site
     * @param Params $params
     * @return ReportResultInterface|false
     */
    public function getDSPSiteReport(AdNetworkInterface $adNetwork, SiteInterface $site, Params $params);

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
     * @param PublisherInterface $publisher
     * @param Params $params
     * @return ReportResultInterface|false
     */
    public function getPublisherAdSlotsReport(PublisherInterface $publisher, Params $params);

    /**
     * @param ReportableAdSlotInterface $adSlot
     * @param Params $params
     * @return ReportResultInterface|false
     */
    public function getAdSlotReport(ReportableAdSlotInterface $adSlot, Params $params);

    /**
     * @param RonAdSlotInterface $ronAdSlot
     * @param Params $params
     * @return ReportResultInterface|false
     */
    public function getRonAdSlotReport(RonAdSlotInterface $ronAdSlot, Params $params);

    /**
     * @param PublisherInterface $publisher
     * @param Params $params
     * @return mixed
     */
    public function getPublisherRonAdSlotReport(PublisherInterface $publisher, Params $params);

    /**
     * @param RonAdSlotInterface $ronAdSlot
     * @param Params $params
     * @return mixed
     */
    public function getRonAdSlotSegmentReport(RonAdSlotInterface $ronAdSlot, Params $params);

    /**
     * @param RonAdSlotInterface $ronAdSlot
     * @param Params $params
     * @return mixed
     */
    public function getRonAdSlotSiteReport(RonAdSlotInterface $ronAdSlot, Params $params);
}