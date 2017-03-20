<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector;

use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Model\Core\SegmentInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\ReportResultInterface;
use Tagcade\Model\Core\AdNetworkInterface;
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
     * @param $inBanner
     * @return ReportResultInterface|false
     */
    public function getAllPublishersReport(Params $params, $inBanner = false);

    /**
     * @param PublisherInterface $publisher
     * @param Params $params
     * @return mixed
     */
    public function getAllSubPublishersReport(PublisherInterface $publisher, Params $params);

    /**
     * @param AdNetworkInterface $adNetwork
     * @param PublisherInterface $publisher
     * @param Params $params
     * @return mixed
     */
    public function getAllSubPublishersReportByPartner(AdNetworkInterface $adNetwork, PublisherInterface $publisher, Params $params);

    /**
     * @param array $publishers
     * @param Params $params
     * @return mixed
     */
    public function getPublishersReport(array $publishers, Params $params);

    /**
     * @param PublisherInterface $publisher
     * @param Params $params
     * @return ReportResultInterface|false
     */
    public function getPublisherReport(PublisherInterface $publisher, Params $params);

    /**
     * get Publisher AdNetworks Report breakdown by ad network
     *
     * @param PublisherInterface $publisher
     * @param Params $params
     * @return ReportResultInterface|false
     */
    public function getPublisherAdNetworksReport(PublisherInterface $publisher, Params $params);

    /**
     * get Publisher AdNetworks Report Breakdown By Day
     *
     * @param PublisherInterface $publisher
     * @param Params $params
     * @return ReportResultInterface|false
     */
    public function getPublisherAdNetworksByDayReport(PublisherInterface $publisher, Params $params);

    /**
     * get Publisher AdNetworks Report Breakdown By WaterfallTag
     *
     * @param PublisherInterface $publisher
     * @param Params $params
     * @return ReportResultInterface|false
     */
    public function getPublisherAdNetworksByAdTagReport(PublisherInterface $publisher, Params $params);

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
     * @param AdNetworkInterface $adNetwork
     * @param Params $params
     * @return ReportResultInterface|false
     */
    public function getAdNetworkAdTagsReport(AdNetworkInterface $adNetwork, Params $params);

    /**
     * @param AdNetworkInterface $adNetwork
     * @param SiteInterface $site
     * @param Params $params
     * @return ReportResultInterface|false
     */
    public function getAdNetworkSiteAdTagsReport(AdNetworkInterface $adNetwork, SiteInterface $site, Params $params);

    /**
     * @param Params $params
     * @return ReportResultInterface|false
     */
    public function getAllSitesReport(Params $params);

    /**
     * @param array $sites
     * @param Params $params
     * @return mixed
     */
    public function getSitesReport(array $sites, Params $params);

    /**
     * get Publisher Sites Report breakdown by day
     *
     * @param PublisherInterface $publisher
     * @param Params $params
     * @return ReportResultInterface|false
     */
    public function getPublisherSitesByDayReport(PublisherInterface $publisher, Params $params);

    /**
     * get Publisher Sites Report breakdown by site
     *
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
    public function getSiteAdNetworksReport(SiteInterface $site, Params $params);

    /**
     * @param SiteInterface $site
     * @param Params $params
     * @return ReportResultInterface|false
     */
    public function getSiteAdSlotsReport(SiteInterface $site, Params $params);

    /**
     * @param SiteInterface $site
     * @param Params $params
     * @return ReportResultInterface|false
     */
    public function getSiteAdTagsReport(SiteInterface $site, Params $params);

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
     * @param ReportableAdSlotInterface $adSlot
     * @param Params $params
     * @return ReportResultInterface|false
     */
    public function getAdSlotAdTagsReport(ReportableAdSlotInterface $adSlot, Params $params);

    /**
     * @param RonAdSlotInterface $ronAdSlot
     * @param Params $params
     * @return ReportResultInterface|false
     */
    public function getRonAdSlotSegmentsReport(RonAdSlotInterface $ronAdSlot, Params $params);

    /**
     * @param RonAdSlotInterface $ronAdSlot
     * @param Params $params
     * @return ReportResultInterface|false
     */
    public function getRonAdSlotSitesReport(RonAdSlotInterface $ronAdSlot, Params $params);

    /**
     * @param RonAdSlotInterface $ronAdSlot
     * @param Params $params
     * @return ReportResultInterface|false
     */
    public function getRonAdSlotAdTagsReport(RonAdSlotInterface $ronAdSlot, Params $params);

    /**
     * @param SegmentInterface $segment
     * @param Params $params
     * @return ReportResultInterface|false
     */
    public function getSegmentReport(SegmentInterface $segment, Params $params);

    /**
     * @param SegmentInterface $segment
     * @param Params $params
     * @return ReportResultInterface|false
     */
    public function getSegmentRonAdSlotsReport(SegmentInterface $segment, Params $params);

    /**
     * @param AdTagInterface $adTag
     * @param Params $params
     * @return ReportResultInterface|false
     */
    public function getAdTagReport(AdTagInterface $adTag, Params $params);

    /**
     * @param Params $params
     * @return mixed
     */
    public function getAllAdSlotsReport(Params $params);

    /**
     * get report of all partners breakdown by partner for a Publisher
     * This is used for comparing with Unified report 'all partners by day for an account'
     * @param PublisherInterface $publisher
     * @param Params $params
     * @return mixed
     */
    public function getAllPartnersReportByPartnerForPublisher(PublisherInterface $publisher, Params $params);


    /**
     * get report of all sites breakdown by day For a Partner
     * This is used for comparing with Unified report 'a partner by day'
     * @param AdNetworkInterface $adNetwork
     * @param Params $params
     * @return mixed
     */
    public function getAllSitesReportByDayForPartner(AdNetworkInterface $adNetwork, Params $params);

    /**
     * get report of all sites breakdown by day For a Partner with a SubPublisher
     * This is used for comparing with Unified report 'a partner by day'
     * @param AdNetworkInterface $adNetwork
     * @param SubPublisherInterface $subPublisher
     * @param Params $params
     * @return mixed
     */
    public function getAllSitesReportByDayForPartnerWithSubPublisher(AdNetworkInterface $adNetwork, SubPublisherInterface $subPublisher, Params $params);
}