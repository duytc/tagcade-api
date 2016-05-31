<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Selector;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tagcade\Bundle\UserBundle\DomainManager\PublisherManagerInterface;
use Tagcade\DomainManager\AdNetworkManagerInterface;
use Tagcade\DomainManager\AdSlotManagerInterface;
use Tagcade\DomainManager\AdTagManagerInterface;
use Tagcade\DomainManager\SiteManagerInterface;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Model\Core\SegmentInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform as PlatformReportTypes;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Segment as SegmentReportTypes;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\AdNetwork as AdNetworkReportTypes;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Partner as PartnerReportTypes;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\SubPublisher as SubPublisherReportTypes;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;
use Tagcade\Service\DateUtilInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\ExpandedReportCollection;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\ReportResultInterface;

class ReportBuilder implements ReportBuilderInterface
{
    const PARAM_START_DATE = 'startDate';
    const PARAM_END_DATE = 'endDate';
    const PARAM_EXPAND = 'expand';
    const PARAM_GROUP = 'group';

    /**
     * @var ReportSelectorInterface
     */
    protected $reportSelector;
    /**
     * @var DateUtilInterface
     */
    protected $dateUtil;
    /**
     * @var PublisherManagerInterface
     */
    protected $userManager;
    /**
     * @var AdNetworkManagerInterface
     */
    protected $adNetworkManager;
    /**
     * @var SiteManagerInterface
     */
    protected $siteManager;
    /**
     * @var AdSlotManagerInterface
     */
    protected $adSlotManager;
    /**
     * @var AdTagManagerInterface
     */
    protected $adTagManager;

    public function __construct(
        ReportSelectorInterface $reportSelector,
        DateUtilInterface $dateUtil,
        PublisherManagerInterface $userManager,
        AdNetworkManagerInterface $adNetworkManager,
        SiteManagerInterface $siteManager,
        AdSlotManagerInterface $adSlotManager,
        AdTagManagerInterface $adTagManager
    )
    {
        $this->reportSelector = $reportSelector;
        $this->dateUtil = $dateUtil;
        $this->userManager = $userManager;
        $this->adNetworkManager = $adNetworkManager;
        $this->siteManager = $siteManager;
        $this->adSlotManager = $adSlotManager;
        $this->adTagManager = $adTagManager;
    }

    public function getPlatformReport(Params $params)
    {
        $publishers = $this->userManager->allPublishers();

        return $this->getReports(new PlatformReportTypes\Platform($publishers), $params);
    }

    public function getAllPublishersReport(Params $params)
    {
        $publishers = $this->userManager->allActivePublishers();

        $reportTypes = array_map(function(PublisherInterface $publisher) {
            return new PlatformReportTypes\Account($publisher);
        }, $publishers);

        return $this->getReports($reportTypes, $params);
    }

    public function getAllSubPublishersReport(PublisherInterface $publisher, Params $params)
    {
        if ($publisher instanceof SubPublisherInterface) {
            throw new AccessDeniedException('you do not have enough permission to view this report');
        }

        $subPublishers = $publisher->getSubPublishers();
        $reportTypes = array_map(function(SUbPublisherInterface $subPublisher) {
            return new SubPublisherReportTypes\SubPublisher($subPublisher);
        }, $subPublishers);

        return $this->getReports($reportTypes, $params);
    }

    public function getAllSubPublishersReportByPartner(AdNetworkInterface $adNetwork, PublisherInterface $publisher, Params $params)
    {
        if ($publisher instanceof SubPublisherInterface) {
            throw new AccessDeniedException('you do not have enough permission to view this report');
        }

        $subPublishers = $publisher->getSubPublishers();
        $reportTypes = array_map(function(SUbPublisherInterface $subPublisher) use ($adNetwork) {
            return new AdNetworkReportTypes\AdNetworkSubPublisher($subPublisher, $adNetwork);
        }, $subPublishers);

        return $this->getReports($reportTypes, $params);
    }

    public function getPublishersReport(array $publishers, Params $params)
    {
        $processedPublishers = [];
        $reportTypes = [];
        foreach ($publishers as $publisher) {

            $id = $publisher instanceof PublisherInterface ? $publisher->getId() : $publisher;
            if (!is_int($id) || (isset($processedPublishers[$id]) && $processedPublishers[$id] === true)) {
                continue;
            }

            $tmpPublisher = $publisher instanceof PublisherInterface ? $publisher : $this->userManager->findPublisher($id);

            $reportTypes[] = new PlatformReportTypes\Account($tmpPublisher);
            $processedPublishers[$id] = true;
        }

        return $this->getReports($reportTypes, $params);
    }

    public function getPublisherReport(PublisherInterface $publisher, Params $params)
    {
        return $this->getReports(new PlatformReportTypes\Account($publisher), $params);
    }

    /**
     * @inheritdoc
     */
    public function getPublisherAdNetworksReport(PublisherInterface $publisher, Params $params)
    {
        $adNetworks = $this->adNetworkManager->getAdNetworksForPublisher($publisher);

        $reportTypes = array_map(function($adNetwork) {
            return new AdNetworkReportTypes\AdNetwork($adNetwork);
        }, $adNetworks);

        return $this->getReports($reportTypes, $params);
    }

    /**
     * @inheritdoc
     */
    public function getPublisherAdNetworksByDayReport(PublisherInterface $publisher, Params $params)
    {
        $reportTypes = ($publisher instanceof SubPublisherInterface)
            ? new SubPublisherReportTypes\SubPublisherAdNetwork($publisher, $adNetwork = null)
            : new AdNetworkReportTypes\AdNetwork($adNetwork = null, $publisher);

        return $this->getReports($reportTypes, $params);
    }

    /**
     * @inheritdoc
     */
    public function getPublisherAdNetworksByAdTagReport(PublisherInterface $publisher, Params $params)
    {
        $adTags = $this->adTagManager->getAdTagsForPublisher($publisher);

        $reportTypes = array_map(function($adTag) {
            return new AdNetworkReportTypes\AdTag($adTag);
        }, $adTags);

        return $this->getReports($reportTypes, $params);
    }

    public function getAdNetworkReport(AdNetworkInterface $adNetwork, Params $params)
    {
        return $this->getReports(new AdNetworkReportTypes\AdNetwork($adNetwork), $params);
    }

    public function getAdnetworkSitesReport(AdNetworkInterface $adNetwork, Params $params)
    {
        $sites = $this->siteManager->getSitesThatHaveAdTagsBelongingToAdNetwork($adNetwork);

        $reportTypes = array_map(function($site) use($adNetwork) {
            return new AdNetworkReportTypes\Site($site, $adNetwork);
        }, $sites);

        return $this->getReports($reportTypes, $params);
    }

    /**
     * @inheritdoc
     */
    public function getAllPartnersReportByPartnerForPublisher(PublisherInterface $publisher, Params $params)
    {
        $adNetworkPartners = $this->adNetworkManager->getAdNetworksThatHavePartnerForPublisher($publisher);

        $reportTypes = array_map(function ($adNetworkPartner) use ($publisher) {
                return ($publisher instanceof SubPublisherInterface)
                    ? new SubPublisherReportTypes\SubPublisherAdNetwork($publisher, $adNetworkPartner)
                    : new AdNetworkReportTypes\AdNetwork($adNetworkPartner);
            }
            , $adNetworkPartners
        );

        return $this->getReports($reportTypes, $params);
    }

    /**
     * @inheritdoc
     */
    public function getAllPartnersReportByDayForPublisher(PublisherInterface $publisher, Params $params)
    {
        return $publisher instanceof SubPublisherInterface
            ? $this->getReports(new SubPublisherReportTypes\SubPublisher($publisher), $params)
            : $this->getReports(new PartnerReportTypes\Account($publisher), $params);
    }

    /**
     * @inheritdoc
     */
    public function getAllPartnersReportBySiteForPublisher(PublisherInterface $publisher, Params $params)
    {
        $sites = $this->siteManager->getSitesThatHaveAdTagsBelongingToPartnerForPublisher($publisher);

        $reportTypes = array_map(function ($site) {
                /** @var SiteInterface $site */
                return new PartnerReportTypes\AdNetworkDomain($partner = null, $site->getDomain());
                // note: also used for SubPublisher
            }
            , $sites
        );

        return $this->getReports($reportTypes, $params);
    }

    /**
     * @inheritdoc
     */
    public function getAllPartnersReportByAdTagForPublisher(PublisherInterface $publisher, Params $params)
    {
        /** @var AdTagInterface[] $adTags */
        $adTags = $this->adTagManager->getAdTagsThatHavePartner($publisher, $uniquePartnerTagId = true);

        $reportTypes = array_map(function($adTag) {
                /** @var AdTagInterface $adTag */
                return new PartnerReportTypes\AdNetworkAdTag($adNetwork = null, $adTag->getPartnerTagId());
                // note: also used for SubPublisher
            }, $adTags);

        return $this->getReports($reportTypes, $params);
    }

    /**
     * @inheritdoc
     */
    public function getAllSitesReportByDayForPartner(AdNetworkInterface $adNetwork, Params $params)
    {
        return $this->getReports(new AdNetworkReportTypes\AdNetwork($adNetwork), $params);
    }

    /**
     * @inheritdoc
     */
    public function getAllSitesReportByDayForPartnerWithSubPublisher(AdNetworkInterface $adNetwork, SubPublisherInterface $subPublisher, Params $params)
    {
        return $this->getReports(new SubPublisherReportTypes\SubPublisherAdNetwork($subPublisher, $adNetwork), $params);
    }

    /**
     * @inheritdoc
     */
    public function getAllSitesReportBySiteForPartner(AdNetworkInterface $adNetwork, Params $params)
    {
        $sites = $this->siteManager->getSitesThatHaveAdTagsBelongingToPartner($adNetwork);

        $reportTypes = array_map(function ($site) use ($adNetwork) {
                /** @var SiteInterface $site */
                return new PartnerReportTypes\AdNetworkDomain($adNetwork, $site->getDomain());
            }
            , $sites
        );

        return $this->getReports($reportTypes, $params);
    }

    /**
     * @inheritdoc
     */
    public function getAllSitesReportBySiteForPartnerWithSubPublisher(AdNetworkInterface $adNetwork, SubPublisherInterface $subPublisher, Params $params)
    {
        $sites = $this->siteManager->getSitesThatHaveAdTagsBelongingToPartnerWithSubPublisher($adNetwork, $subPublisher);

        $reportTypes = array_map(function ($site) use ($adNetwork) {
                /** @var SiteInterface $site */
                return new PartnerReportTypes\AdNetworkDomain($adNetwork, $site->getDomain());
            }
            , $sites
        );

        return $this->getReports($reportTypes, $params);
    }

    /**
     * @inheritdoc
     */
    public function getAllSitesReportByAdTagForPartner(AdNetworkInterface $adNetwork, Params $params)
    {
        $adTags = $this->adTagManager->getAdTagsThatHavePartnerForAdNetwork($adNetwork);

        $reportTypes = array_map(function($adTag) use ($adNetwork) {
            /** @var AdTagInterface $adTag */
            return new PartnerReportTypes\AdNetworkAdTag($adNetwork, $adTag->getPartnerTagId());
        }, $adTags);

        return $this->getReports($reportTypes, $params);
    }

    /**
     * @inheritdoc
     */
    public function getAllSitesReportByAdTagForPartnerWithSubPublisher(AdNetworkInterface $adNetwork, SubPublisherInterface $subPublisher, Params $params)
    {
        $adTags = $this->adTagManager->getAdTagsThatHavePartnerForAdNetworkWithSubPublisher($adNetwork, $subPublisher);

        $reportTypes = array_map(function($adTag) use ($adNetwork) {
            /** @var AdTagInterface $adTag */
            return new PartnerReportTypes\AdNetworkAdTag($adNetwork, $adTag->getPartnerTagId());
        }, $adTags);

        return $this->getReports($reportTypes, $params);
    }

    /**
     * @inheritdoc
     */
    public function getSiteReportByDayForPartner(AdNetworkInterface $adNetwork, SiteInterface $site, Params $params)
    {
        // note: also used for SubPublisher
        return $this->getReports(new PartnerReportTypes\AdNetworkDomain($adNetwork, $site->getDomain()), $params);
    }

    /**
     * @inheritdoc
     */
    public function getSiteReportByAdTagForPartner(AdNetworkInterface $adNetwork, SiteInterface $site, Params $params)
    {
        $adTags = $this->adTagManager->getAdTagsForAdNetworkAndSite($adNetwork, $site);

        $reportTypes = array_map(function($adTag) use ($adNetwork) {
            /** @var AdTagInterface $adTag */
            return new PartnerReportTypes\AdNetworkAdTag($adNetwork, $adTag->getPartnerTagId());
        }, $adTags);

        return $this->getReports($reportTypes, $params);
    }

    /**
     * @inheritdoc
     */
    public function getSiteReportByAdTagForPartnerWithSubPublisher(AdNetworkInterface $adNetwork, SiteInterface $site, SubPublisherInterface $subPublisher, Params $params)
    {
        $adTags = $this->adTagManager->getAdTagsForAdNetworkAndSiteWithSubPublisher($adNetwork, $site, $subPublisher);

        $reportTypes = array_map(function($adTag) use ($adNetwork, $site, $subPublisher) {
            /** @var AdTagInterface $adTag */
            return new PartnerReportTypes\AdNetworkDomainAdTagSubPublisher($adNetwork, $site->getDomain(), $adTag->getPartnerTagId(), $subPublisher);
        }, $adTags);

        return $this->getReports($reportTypes, $params);
    }

    public function getAdNetworkSiteReport(AdNetworkInterface $adNetwork, SiteInterface $site, Params $params)
    {
        return $this->getReports(new AdNetworkReportTypes\Site($site, $adNetwork), $params);
    }

    public function getAdNetworkAdTagsReport(AdNetworkInterface $adNetwork, Params $params)
    {
        $adTags = $this->adTagManager->getAdTagsForAdNetwork($adNetwork);

        $reportTypes = array_map(function($adTag) {
            return new AdNetworkReportTypes\AdTag($adTag);
        }, $adTags);

        return $this->getReports($reportTypes, $params);
    }

    public function getAdNetworkSiteAdTagsReport(AdNetworkInterface $adNetwork, Siteinterface $site, Params $params)
    {
        $adTags = $this->adTagManager->getAdTagsForAdNetworkAndSite($adNetwork, $site);

        $reportTypes = array_map(function($adTag) {
            return new AdNetworkReportTypes\AdTag($adTag);
        }, $adTags);

        return $this->getReports($reportTypes, $params);
    }

    public function getAllSitesReport(Params $params)
    {
        $sites = $this->siteManager->all();

        $reportTypes = array_map(function(SiteInterface $site) {
            return new PlatformReportTypes\Site($site);
        }, $sites);

        return $this->getReports($reportTypes, $params);
    }

    public function getSitesReport(array $sites, Params $params)
    {
        $reportTypes = [];
        $enqueuedSites = [];

        foreach ($sites as $site) {
            $id = $site instanceof SiteInterface ? $site->getId() : $site;
            if (!is_int($id) || $id < 0) {
                continue;
            }

            if (isset($enqueuedSites[$id]) && $enqueuedSites[$id] === true) {
                continue;
            }

            $enqueuedSites[$id] = true;

            $mySite = $site instanceof SiteInterface ? $site : $this->siteManager->find($id);
            $reportTypes[] = new PlatformReportTypes\Site($mySite);
        }

        return $this->getReports($reportTypes, $params);
    }

    /**
     * @inheritdoc
     */
    public function getPublisherSitesByDayReport(PublisherInterface $publisher, Params $params)
    {
        $reportTypes = ($publisher instanceof SubPublisherInterface)
            ? new SubPublisherReportTypes\SubPublisher($publisher)
            : new PlatformReportTypes\Account($publisher);

        return $this->getReports($reportTypes, $params);
    }

    /**
     * @inheritdoc
     */
    public function getPublisherSitesReport(PublisherInterface $publisher, Params $params)
    {
        $sites = $this->siteManager->getSitesForPublisher($publisher);

        $reportTypes = array_map(function($site) {
            return new PlatformReportTypes\Site($site);
        }, $sites);

        return $this->getReports($reportTypes, $params);
    }

    public function getSiteReport(SiteInterface $site, Params $params)
    {
        return $this->getReports(new PlatformReportTypes\Site($site), $params);
    }

    public function getSiteAdNetworksReport(SiteInterface $site, Params $params)
    {
        $adNetworks = $this->getAdNetworksForSite($site);

        $reportTypes = array_map(
            function ($adNetwork) use($site) {
                return new AdNetworkReportTypes\Site($site, $adNetwork, $groupByAdNetwork = true);
            },
            $adNetworks
        );

        return $this->getReports($reportTypes, $params);
    }

    public function getSiteAdSlotsReport(SiteInterface $site, Params $params)
    {
        $adSlots = $site->getReportableAdSlots();

        $reportTypes = array_map(function($adSlot) {
            return new PlatformReportTypes\AdSlot($adSlot);
        }, $adSlots);

        return $this->getReports($reportTypes, $params);
    }

    public function getSiteAdTagsReport(SiteInterface $site, Params $params)
    {
        $siteReport = $this->getSiteReport($site, $params);

        if (!$siteReport) {
            return false;
        }

        $adTags = $this->adTagManager->getAdTagsForSite($site);

        $reportTypes = array_map(function($adTag) {
            return new PlatformReportTypes\AdTag($adTag);
        }, $adTags);

        $adTagReports = $this->getReports($reportTypes, $params);

        if (!$adTagReports) {
            return false;
        }

        return new ExpandedReportCollection($adTagReports, $siteReport);
    }

    public function getPublisherAdSlotsReport(PublisherInterface $publisher, Params $params)
    {
        $adSlots = $this->adSlotManager->getReportableAdSlotsForPublisher($publisher);

        $reportTypes = array_map(function($adSlot) {
            return new PlatformReportTypes\AdSlot($adSlot);
        }, $adSlots);

        return $this->getReports($reportTypes, $params);
    }

    public function getAdSlotReport(ReportableAdSlotInterface $adSlot, Params $params)
    {
        return $this->getReports(new PlatformReportTypes\AdSlot($adSlot), $params);
    }

    public function getAllAdSlotsReport(Params $params)
    {
        $adSlots = $this->adSlotManager->allReportableAdSlots();
        $reportTypes = array_map(function($adSlot) {
            return new PlatformReportTypes\AdSlot($adSlot);
        }, $adSlots);

        return $this->getReports($reportTypes, $params);
    }

    public function getRonAdSlotReport(RonAdSlotInterface $adSlot, Params $params)
    {
        return $this->getReports(new SegmentReportTypes\RonAdSlot($adSlot), $params);
    }

    public function getAdSlotAdTagsReport(ReportableAdSlotInterface $adSlot, Params $params)
    {
        $adSlotReport = $this->getAdSlotReport($adSlot, $params);

        if (!$adSlotReport) {
            return false;
        }

        $adTags = $adSlot->getAdTags()->toArray();

        $reportTypes = array_map(function($adTag) {
            return new PlatformReportTypes\AdTag($adTag);
        }, $adTags);

        $adTagReports = $this->getReports($reportTypes, $params);

        if (!$adTagReports) {
            return false;
        }

        return new ExpandedReportCollection($adTagReports, $adSlotReport);
    }

    /**
     * @param RonAdSlotInterface $ronAdSlot
     * @param Params $params
     * @return ReportResultInterface|false
     */
    public function getRonAdSlotSegmentsReport(RonAdSlotInterface $ronAdSlot, Params $params)
    {
        $segments = $ronAdSlot->getSegments();

        $reportTypes = array_map(function($segment) use ($ronAdSlot){
            return new SegmentReportTypes\RonAdSlot($ronAdSlot, $segment);
        }, $segments);

        return $this->getReports($reportTypes, $params);
    }


    /**
     * @param RonAdSlotInterface $ronAdSlot
     * @param Params $params
     * @return ReportResultInterface|false
     */
    public function getRonAdSlotSitesReport(RonAdSlotInterface $ronAdSlot, Params $params)
    {
        $ronAdSlotReport = $this->getRonAdSlotReport($ronAdSlot, $params);

        if (!$ronAdSlotReport) {
            return false;
        }

        $adSlots = $this->adSlotManager->getAdSlotsForRonAdSlot($ronAdSlot);

        $reportTypes = array_map(function($adSlot) {
            return new PlatformReportTypes\AdSlot($adSlot);
        }, $adSlots);

        $adSlotReports =  $this->getReports($reportTypes, $params);

        if (!$adSlotReports) {
            return false;
        }

        return new ExpandedReportCollection($adSlotReports, $ronAdSlotReport);
    }

    /**
     * @param RonAdSlotInterface $ronAdSlot
     * @param Params $params
     * @return ReportResultInterface|false
     */
    public function getRonAdSlotAdTagsReport(RonAdSlotInterface $ronAdSlot, Params $params)
    {
        $ronAdSlotReport = $this->getRonAdSlotReport($ronAdSlot, $params);

        if (!$ronAdSlotReport) {
            return false;
        }

        $ronAdTags = $ronAdSlot->getRonAdTags();

        $reportTypes = array_map(function($ronAdTag) {
            return new SegmentReportTypes\RonAdTag($ronAdTag);
        }, $ronAdTags);

        $ronAdTagReports = $this->getReports($reportTypes, $params);

        if (!$ronAdTagReports) {
            return false;
        }

        return new ExpandedReportCollection($ronAdTagReports, $ronAdSlotReport);
    }

    /**
     * @param SegmentInterface $segment
     * @param Params $params
     * @return ReportResultInterface|false
     */
    public function getSegmentReport(SegmentInterface $segment, Params $params)
    {
        return $this->getReports(new SegmentReportTypes\Segment($segment), $params);
    }

    /**
     * @param SegmentInterface $segment
     * @param Params $params
     * @return ReportResultInterface|false
     */
    public function getSegmentRonAdSlotsReport(SegmentInterface $segment, Params $params)
    {
        $ronAdSlots = $segment->getRonAdSlots();

        $reportTypes = array_map(function($ronAdSlot) {
            return new SegmentReportTypes\RonAdSlot($ronAdSlot);
        }, $ronAdSlots);

        return $this->getReports($reportTypes, $params);
    }


    public function getAdTagReport(AdTagInterface $adTag, Params $params)
    {
        return $this->getReports(new PlatformReportTypes\AdTag($adTag), $params);
    }

    /**
     * @param ReportTypeInterface|ReportTypeInterface[] $reportType
     * @param Params $params
     * @return ReportResultInterface|false
     */
    protected function getReports($reportType, Params $params)
    {
        if (is_array($reportType)) {
            return $this->reportSelector->getMultipleReports($reportType, $params);
        }

        return $this->reportSelector->getReports($reportType, $params);
    }

    /**
     * @param SiteInterface $site
     * @return AdNetworkInterface[]
     * @todo this should be a method of the AdNetworkManager
     */
    protected function getAdNetworksForSite(SiteInterface $site)
    {
        $adNetworks = [];

        $adTags = $this->adTagManager->getAdTagsForSite($site);

        foreach ($adTags as $adTag) {
            if (!in_array($adTag->getAdNetwork(), $adNetworks, $strict = true)) {
                $adNetworks[] = $adTag->getAdNetwork();
            }
        }

        return $adNetworks;
    }
}