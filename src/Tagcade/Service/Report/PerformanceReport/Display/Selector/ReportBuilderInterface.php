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
     * @return ReportResultInterface|false
     */
    public function getAllPublishersReport(Params $params);

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
     * @param PublisherInterface $publisher
     * @param Params $params
     * @return ReportResultInterface|false
     */
    public function getPublisherAdNetworksReport(PublisherInterface $publisher, Params $params);

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
     * get report of all partners breakdown by day for a Publisher
     * This is used for comparing with Unified report 'all partners by day for an account'
     * @param PublisherInterface $publisher
     * @param Params $params
     * @return mixed
     */
    public function getAllPartnersReportByDayForPublisher(PublisherInterface $publisher, Params $params);

    /**
     * get report of all partners breakdown by domain for a Publisher
     * This is used for comparing with Unified report 'all partners by domain for an account'
     * @param PublisherInterface $publisher
     * @param Params $params
     * @return mixed
     */
    public function getAllPartnersReportBySiteForPublisher(PublisherInterface $publisher, Params $params);

    /**
     * get report of all partners breakdown by ad tag for a Publisher
     * This is used for comparing with Unified report 'all partners by ad tag for an account'
     * @param PublisherInterface $publisher
     * @param Params $params
     * @return mixed
     */
    public function getAllPartnersReportByAdTagForPublisher(PublisherInterface $publisher, Params $params);

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

    /**
     * get report of all sites breakdown by domain For a Partner
     * This is used for comparing with Unified report 'a partners by domain'
     * @param AdNetworkInterface $adNetwork
     * @param Params $params
     * @return mixed
     */
    public function getAllSitesReportBySiteForPartner(AdNetworkInterface $adNetwork, Params $params);

    /**
     * get report of all sites breakdown by domain For a Partner
     * This is used for comparing with Unified report 'a partners by domain'
     * @param AdNetworkInterface $adNetwork
     * @param SubPublisherInterface $subPublisher
     * @param Params $params
     * @return mixed
     */
    public function getAllSitesReportBySiteForPartnerWithSubPublisher(AdNetworkInterface $adNetwork, SubPublisherInterface $subPublisher, Params $params);

    /**
     * get report of all sites breakdown by ad tag For a Partner
     * This is used for comparing with Unified report 'a partners by ad tag'
     * @param AdNetworkInterface $adNetwork
     * @param Params $params
     * @return mixed
     */
    public function getAllSitesReportByAdTagForPartner(AdNetworkInterface $adNetwork, Params $params);

    /**
     * get report of all sites breakdown by ad tag For a Partner
     * This is used for comparing with Unified report 'a partners by ad tag'
     * @param AdNetworkInterface $adNetwork
     * @param SubPublisherInterface $subPublisher
     * @param Params $params
     * @return mixed
     */
    public function getAllSitesReportByAdTagForPartnerWithSubPublisher(AdNetworkInterface $adNetwork, SubPublisherInterface $subPublisher, Params $params);

    /**
     * get report of a site breakdown by day For a Partner
     * This is used for comparing with Unified report 'a partner by day'
     * @param AdNetworkInterface $adNetwork
     * @param SiteInterface $site
     * @param Params $params
     * @return mixed
     */
    public function getSiteReportByDayForPartner(AdNetworkInterface $adNetwork, SiteInterface $site, Params $params);

    /**
     * get report of a site breakdown by ad tag For a Partner
     * This is used for comparing with Unified report 'a partners by ad tag'
     * @param AdNetworkInterface $adNetwork
     * @param SiteInterface $site
     * @param Params $params
     * @return mixed
     */
    public function getSiteReportByAdTagForPartner(AdNetworkInterface $adNetwork, SiteInterface $site, Params $params);

    /**
     * get report of a site breakdown by ad tag For a Partner with SubPublisher
     * This is used for comparing with Unified report 'a partner domain by ad tag for a subPublisher'
     * @param AdNetworkInterface $adNetwork
     * @param SiteInterface $site
     * @param SubPublisherInterface $subPublisher
     * @param Params $params
     * @return mixed
     */
    public function getSiteReportByAdTagForPartnerWithSubPublisher(AdNetworkInterface $adNetwork, SiteInterface $site, SubPublisherInterface $subPublisher, Params $params);
}