<?php

namespace Tagcade\Service\Report\UnifiedReport\Selector;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tagcade\Bundle\UserBundle\DomainManager\PublisherManagerInterface;
use Tagcade\DomainManager\AdNetworkManagerInterface;
use Tagcade\DomainManager\AdSlotManagerInterface;
use Tagcade\DomainManager\AdTagManagerInterface;
use Tagcade\DomainManager\SiteManagerInterface;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Model\Report\UnifiedReport\ReportType\Comparison as ComparisonReportTypes;
use Tagcade\Model\Report\UnifiedReport\ReportType\Network as NetworkReportTypes;
use Tagcade\Model\Report\UnifiedReport\ReportType\Publisher as PublisherReportTypes;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;
use Tagcade\Repository\Report\UnifiedReport\Network\NetworkAdTagReportRepositoryInterface;
use Tagcade\Repository\Report\UnifiedReport\Network\NetworkSiteReportRepositoryInterface;
use Tagcade\Service\DateUtilInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\ReportResultInterface;

class ReportBuilder implements ReportBuilderInterface
{
    const PARAM_START_DATE = 'startDate';
    const PARAM_END_DATE = 'endDate';
    const PARAM_EXPAND = 'expand';
    const PARAM_GROUP = 'group';

    /** @var ReportSelectorInterface */
    protected $reportSelector;

    /** @var DateUtilInterface */
    protected $dateUtil;

    /** @var PublisherManagerInterface */
    protected $userManager;

    /** @var AdNetworkManagerInterface */
    protected $adNetworkManager;

    /** @var SiteManagerInterface */
    protected $siteManager;

    /** @var AdSlotManagerInterface */
    protected $adSlotManager;

    /** @var AdTagManagerInterface */
    protected $adTagManager;

    /** @var NetworkAdTagReportRepositoryInterface */
    protected $networkAdTagRepository;

    /** @var NetworkSiteReportRepositoryInterface */
    protected $networkSiteRepository;

    public function __construct(
        ReportSelectorInterface $reportSelector,
        DateUtilInterface $dateUtil,
        PublisherManagerInterface $userManager,
        AdNetworkManagerInterface $adNetworkManager,
        SiteManagerInterface $siteManager,
        AdTagManagerInterface $adTagManager,
        NetworkAdTagReportRepositoryInterface $networkAdTagRepository,
        NetworkSiteReportRepositoryInterface $networkSiteRepository
    )
    {
        $this->reportSelector = $reportSelector;
        $this->dateUtil = $dateUtil;
        $this->userManager = $userManager;
        $this->adNetworkManager = $adNetworkManager;
        $this->siteManager = $siteManager;
        $this->adTagManager = $adTagManager;
        $this->networkAdTagRepository = $networkAdTagRepository;
        $this->networkSiteRepository = $networkSiteRepository;
    }

    /**
     * @inheritdoc
     */
    public function getAllDemandPartnersByPartnerReport(PublisherInterface $publisher, Params $params)
    {
        $adNetworkPartners = $this->adNetworkManager->getAdNetworksThatHavePartnerForPublisher($publisher);

        $reportTypes = array_map(function ($adNetworkPartner) use ($publisher) {
                return ($publisher instanceof SubPublisherInterface)
                    ? new PublisherReportTypes\SubPublisherNetwork($adNetworkPartner, $publisher)
                    : new NetworkReportTypes\Network($adNetworkPartner);
            }
            , $adNetworkPartners
        );

        return $this->getReports($reportTypes, $params);
    }

    /**
     * @inheritdoc
     */
    public function getAllDemandPartnersByDayReport(PublisherInterface $publisher, Params $params)
    {
        return ($publisher instanceof SubPublisherInterface)
            ? $this->getReports(new PublisherReportTypes\SubPublisher($publisher), $params)
            : $this->getReports(new PublisherReportTypes\Publisher($publisher), $params);
    }

    /**
     * @inheritdoc
     */
    public function getAllDemandPartnersBySiteReport(PublisherInterface $publisher, Params $params)
    {
        $domains = $this->networkSiteRepository->getAllDistinctDomains($params);

        $reportTypes = array_map(function ($domain) use ($publisher) {
            return $publisher instanceof SubPublisherInterface
                ? new NetworkReportTypes\NetworkSiteSubPublisher(null, $domain['domain'], $publisher)
                : new NetworkReportTypes\NetworkSite(null, $domain['domain']);
        }, $domains);

        return $this->getReports($reportTypes, $params);
    }

    /**
     * @inheritdoc
     */
    public function getAllDemandPartnersByDayDiscrepancyReport(PublisherInterface $publisher, Params $params)
    {
        return $this->reportSelector->getDiscrepancies(new PublisherReportTypes\Publisher($publisher), $params);
    }

    /**
     * @inheritdoc
     */
    public function getAllDemandPartnersBySiteDiscrepancyReport(PublisherInterface $publisher, Params $params)
    {
        return $this->reportSelector->getDiscrepancies(new NetworkReportTypes\NetworkSite(null, null), $params);
    }

    /**
     * @inheritdoc
     */
    public function getAllDemandPartnersByAdTagReport(PublisherInterface $publisher, Params $params)
    {
        $adTags = $this->networkAdTagRepository->getAllDistinctAdTags($params);

        $reportTypes = array_map(function (array $adTag) use ($publisher) {
            $partnerTagId = $adTag['partnerTagId'];

            return $publisher instanceof SubPublisherInterface
                ? new NetworkReportTypes\NetworkAdTagSubPublisher(null, $partnerTagId, $publisher)
                : new NetworkReportTypes\NetworkAdTag(null, $partnerTagId);
        }, $adTags);

        return $this->getReports($reportTypes, $params);
    }

    /**
     * @inheritdoc
     */
    public function getAllDemandPartnersByAdTagReportForSubPublisher(SubPublisherInterface $publisher, Params $params)
    {
        $adTags = $this->adTagManager->getAdTagsThatHavePartner($publisher);

        $reportTypes = array_map(function (AdTagInterface $adTag) {
            return new NetworkReportTypes\NetworkAdTag(null, $adTag->getPartnerTagId());
        }, $adTags);

        return $this->getReports($reportTypes, $params);
    }

    /**
     * @inheritdoc
     */
    public function getPartnerAllSitesByDayReport(AdNetworkInterface $adNetwork, Params $params)
    {
        return $this->getReports(new NetworkReportTypes\Network($adNetwork), $params);
    }

    /**
     * @inheritdoc
     */
    public function getPartnerAllSitesByDayForSubPublisherReport(SubPublisherInterface $subPublisher, AdNetworkInterface $adNetwork, Params $params)
    {
        return $this->getReports(new PublisherReportTypes\SubPublisherNetwork($adNetwork, $subPublisher), $params);
    }

    /**
     * @inheritdoc
     */
    public function getPartnerByDayDiscrepancyReport(AdNetworkInterface $adNetwork, Params $params)
    {
        return $this->reportSelector->getDiscrepancies(new NetworkReportTypes\Network($adNetwork), $params);
    }

    /**
     * @inheritdoc
     */
    public function getPartnerAllSitesBySitesReport(PublisherInterface $publisher, AdNetworkInterface $adNetwork, Params $params)
    {
        $domains = $this->networkSiteRepository->getAllDistinctDomainsForPartner($adNetwork, $params);

        $reportTypes = array_map(function ($domain) use ($adNetwork, $publisher) {
            return $publisher instanceof SubPublisherInterface
                ? new NetworkReportTypes\NetworkSiteSubPublisher($adNetwork, $domain['domain'], $publisher)
                : new NetworkReportTypes\NetworkSite($adNetwork, $domain['domain']);
        }, $domains);

        return $this->getReports($reportTypes, $params);
    }

    /**
     * @inheritdoc
     */
    public function getPartnerAllSitesByAdTagsReport(AdNetworkInterface $adNetwork, Params $params)
    {
        $adTags = $this->networkAdTagRepository->getAllDistinctAdTagsForPartner($adNetwork, $params);

        $reportTypes = array_map(function (array $adTag) use ($adNetwork) {
            return new NetworkReportTypes\NetworkAdTag($adNetwork, $adTag['partnerTagId']);
        }, $adTags);

        return $this->getReports($reportTypes, $params);
    }

    public function getPartnerByAdTagsForSubPublisherReport(SubPublisherInterface $subPublisher, AdNetworkInterface $adNetwork, Params $params)
    {
        $adTags = $this->networkAdTagRepository->getAllDistinctAdTagsForPartner($adNetwork, $params);

        $reportTypes = array_map(function (array $adTag) use ($adNetwork, $subPublisher) {
            return new NetworkReportTypes\NetworkAdTagSubPublisher($adNetwork, $adTag['partnerTagId'], $subPublisher);
        }, $adTags);

        return $this->getReports($reportTypes, $params);
    }

    /**
     * @inheritdoc
     */
    public function getPartnerSiteByAdTagsReport(AdNetworkInterface $adNetwork, SiteInterface $site, Params $params)
    {
        $adTags = $this->adTagManager->getAdTagsForAdNetworkAndSite($adNetwork, $site);

        $this->removeDuplicatedPartnerTagId($adTags);

        $reportTypes = array_map(function (AdTagInterface $adTag) use ($adNetwork, $site) {
            return new NetworkReportTypes\NetworkDomainAdTag($adNetwork, $site->getDomain(), $adTag->getPartnerTagId());
        }, $adTags);

        return $this->getReports($reportTypes, $params);
    }

    public function getPartnerSiteByAdTagsForSubPublisherReport(SubPublisherInterface $subPublisher, AdNetworkInterface $adNetwork, SiteInterface $site, Params $params)
    {
        $adTags = $this->adTagManager->getAdTagsForAdNetworkAndSite($adNetwork, $site);

        $this->removeDuplicatedPartnerTagId($adTags);

        $reportTypes = array_map(function (AdTagInterface $adTag) use ($subPublisher, $adNetwork, $site) {
            return new NetworkReportTypes\NetworkDomainAdTagSubPublisher($subPublisher, $adNetwork, $site->getDomain(), $adTag->getPartnerTagId());
        }, $adTags);

        return $this->getReports($reportTypes, $params);
    }

    /**
     * @inheritdoc
     */
    public function getPartnerSiteByDaysReport(AdNetworkInterface $adNetwork, $domain, Params $params)
    {
        return $this->getReports(new NetworkReportTypes\NetworkSite($adNetwork, $domain), $params);
    }

    public function getPartnerSiteByDaysForSubPublisherReport(SubPublisherInterface $subPublisher, AdNetworkInterface $adNetwork, $domain, Params $params)
    {
        return $this->getReports(new NetworkReportTypes\NetworkSiteSubPublisher($adNetwork, $domain, $subPublisher), $params);
    }

    public function getSubPublishersReport(PublisherInterface $publisher, Params $params)
    {
        if ($publisher instanceof SubPublisherInterface) {
            throw new AccessDeniedException('You do not have enough permission to view this report');
        }

        $subPublishers = $publisher->getSubPublishers();
        $reportTypes = array_map(function ($subPublisher) use ($publisher) {
            return new PublisherReportTypes\SubPublisher($subPublisher);
            }
            , $subPublishers
        );

        return $this->getReports($reportTypes, $params);
    }

    public function getSubPublishersDiscrepancyReport(PublisherInterface $publisher, Params $params)
    {
        if ($publisher instanceof SubPublisherInterface) {
            throw new AccessDeniedException('You do not have enough permission to view this report');
        }

        $subPublishers = $publisher->getSubPublishers();
        $reportTypes = array_map(function ($subPublisher) use ($publisher) {
            return new ComparisonReportTypes\SubPublisher($subPublisher);
        }
            , $subPublishers
        );

        return $this->getReports($reportTypes, $params);
    }

    public function getSubPublishersReportByPartner(AdNetworkInterface $adNetwork, PublisherInterface $publisher, Params $params)
    {
        if ($publisher instanceof SubPublisherInterface) {
            throw new AccessDeniedException('You do not have enough permission to view this report');
        }

        $subPublishers = $publisher->getSubPublishers();
        $reportTypes = array_map(function ($subPublisher) use ($publisher, $adNetwork) {
            return new NetworkReportTypes\NetworkSubPublisher($adNetwork, $subPublisher);
            }, $subPublishers
        );

        return $this->getReports($reportTypes, $params);
    }

    public function getSubPublishersDiscrepancyReportByPartner(AdNetworkInterface $adNetwork, PublisherInterface $publisher, Params $params)
    {
        if ($publisher instanceof SubPublisherInterface) {
            throw new AccessDeniedException('You do not have enough permission to view this report');
        }

        $subPublishers = $publisher->getSubPublishers();
        $reportTypes = array_map(function ($subPublisher) use ($publisher, $adNetwork) {
            return new ComparisonReportTypes\AdNetworkSubPublisher($adNetwork, $subPublisher);
        }
            , $subPublishers
        );

        return $this->getReports($reportTypes, $params);
    }

    /**
     * @inheritdoc
     */
    public function getAllPartnersDiscrepancyByPartnerForPublisher(PublisherInterface $publisher, Params $params)
    {
        $adNetworkPartners = $this->adNetworkManager->getAdNetworksThatHavePartnerForPublisher($publisher);

        $reportTypes = array_map(function ($adNetworkPartner) use ($publisher) {
                return ($publisher instanceof SubPublisherInterface)
                    ? new ComparisonReportTypes\SubPublisherAdNetwork($adNetworkPartner, $publisher)
                    : new ComparisonReportTypes\AdNetwork($adNetworkPartner);
            }
            , $adNetworkPartners
        );

        return $this->getReports($reportTypes, $params);
    }

    /**
     * @inheritdoc
     */
    public function getAllPartnersDiscrepancyByDayForPublisher(PublisherInterface $publisher, Params $params)
    {
        $report = $publisher instanceof SubPublisherInterface
            ? $this->getReports(new ComparisonReportTypes\SubPublisher($publisher), $params)
            : $this->getReports(new ComparisonReportTypes\Account($publisher), $params);
        return $report;
    }

    /**
     * @inheritdoc
     */
    public function getAllPartnersDiscrepancyBySiteForPublisher(PublisherInterface $publisher, Params $params)
    {
        $sites = $this->siteManager->getSitesThatHaveAdTagsBelongingToPartnerForPublisher($publisher);

        $reportTypes = array_map(function ($site) {
                /** @var SiteInterface $site */
                return new ComparisonReportTypes\AdNetworkDomain($partner = null, $site->getDomain());
                // note: also used for SubPublisher
            }
            , $sites
        );

        return $this->getReports($reportTypes, $params);
    }

    /**
     * @inheritdoc
     */
    public function getAllPartnersDiscrepancyByAdTagForPublisher(PublisherInterface $publisher, Params $params)
    {
        $adTags = $this->adTagManager->getAdTagsThatHavePartner($publisher, $uniquePartnerTagId = true);

        $reportTypes = array_map(function ($adTag) {
            /** @var AdTagInterface $adTag */
            $partnerTagId = $adTag->getPartnerTagId();

            // note: also used for SubPublisher
            return new ComparisonReportTypes\AdNetworkAdTag($adTag->getAdNetwork(), $partnerTagId);
        }, $adTags);

        return $this->getReports($reportTypes, $params);
    }

    /**
     * @inheritdoc
     */
    public function getAllSitesDiscrepancyByDayForPartner(AdNetworkInterface $adNetwork, Params $params)
    {
        return $this->getReports(new ComparisonReportTypes\AdNetwork($adNetwork), $params);
    }

    /**
     * @inheritdoc
     */
    public function getAllSitesDiscrepancyByDayForPartnerWithSubPublisher(AdNetworkInterface $adNetwork, SubPublisherInterface $subPublisher, Params $params)
    {
        return $this->getReports(new ComparisonReportTypes\SubPublisherAdNetwork($adNetwork, $subPublisher), $params);
    }

    /**
     * @inheritdoc
     */
    public function getAllSitesDiscrepancyBySiteForPartner(AdNetworkInterface $adNetwork, Params $params)
    {
        $sites = $this->siteManager->getSitesThatHaveAdTagsBelongingToPartner($adNetwork);

        $reportTypes = array_map(function ($site) use ($adNetwork) {
                /** @var SiteInterface $site */
                return new ComparisonReportTypes\AdNetworkDomain($adNetwork, $site->getDomain());
            }
            , $sites
        );

        return $this->getReports($reportTypes, $params);
    }

    /**
     * @inheritdoc
     */
    public function getAllSitesDiscrepancyBySiteForPartnerWithSubPublisher(AdNetworkInterface $adNetwork, SubPublisherInterface $subPublisher, Params $params)
    {
        $sites = $this->siteManager->getSitesThatHaveAdTagsBelongingToPartnerWithSubPublisher($adNetwork, $subPublisher);

        $reportTypes = array_map(function ($site) use ($adNetwork) {
                /** @var SiteInterface $site */
                return new ComparisonReportTypes\AdNetworkDomain($adNetwork, $site->getDomain());
            }
            , $sites
        );

        return $this->getReports($reportTypes, $params);
    }

    /**
     * @inheritdoc
     */
    public function getAllSitesDiscrepancyByAdTagForPartner(AdNetworkInterface $adNetwork, Params $params)
    {
        $adTags = $this->adTagManager->getAdTagsThatHavePartnerForAdNetwork($adNetwork);
        $this->removeDuplicatedPartnerTagId($adTags);

        $reportTypes = array_map(function ($adTag) use ($adNetwork) {
            /** @var AdTagInterface $adTag */
            return new ComparisonReportTypes\AdNetworkAdTag($adNetwork, $adTag->getPartnerTagId());
        }, $adTags);

        return $this->getReports($reportTypes, $params);
    }

    /**
     * @inheritdoc
     */
    public function getAllSitesDiscrepancyByAdTagForPartnerWithSubPublisher(AdNetworkInterface $adNetwork, SubPublisherInterface $subPublisher, Params $params)
    {
        $adTags = $this->adTagManager->getAdTagsThatHavePartnerForAdNetworkWithSubPublisher($adNetwork, $subPublisher);
        $this->removeDuplicatedPartnerTagId($adTags);

        $reportTypes = array_map(function ($adTag) use ($adNetwork) {
            /** @var AdTagInterface $adTag */
            return new ComparisonReportTypes\AdNetworkAdTag($adNetwork, $adTag->getPartnerTagId());
        }, $adTags);

        return $this->getReports($reportTypes, $params);
    }

    /**
     * @inheritdoc
     */
    public function getSiteDiscrepancyByDayForPartner(AdNetworkInterface $adNetwork, SiteInterface $site, Params $params)
    {
        // note: also used for SubPublisher
        return $this->getReports(new ComparisonReportTypes\AdNetworkDomain($adNetwork, $site->getDomain()), $params);
    }

    /**
     * @inheritdoc
     */
    public function getSiteDiscrepancyByAdTagForPartner(AdNetworkInterface $adNetwork, SiteInterface $site, Params $params)
    {
        $adTags = $this->adTagManager->getAdTagsForAdNetworkAndSite($adNetwork, $site);
        $this->removeDuplicatedPartnerTagId($adTags);

        $reportTypes = array_map(function ($adTag) use ($adNetwork, $site) {
            /** @var AdTagInterface $adTag */
            return new ComparisonReportTypes\AdNetworkDomainAdTag($adNetwork, $site->getDomain(), $adTag->getPartnerTagId());
        }, $adTags);

        return $this->getReports($reportTypes, $params);
    }

    /**
     * @inheritdoc
     */
    public function getSiteDiscrepancyByAdTagForPartnerWithSubPublisher(AdNetworkInterface $adNetwork, SiteInterface $site, SubPublisherInterface $subPublisher, Params $params)
    {
        $adTags = $this->adTagManager->getAdTagsForAdNetworkAndSiteWithSubPublisher($adNetwork, $site, $subPublisher);
        $this->removeDuplicatedPartnerTagId($adTags);

        $reportTypes = array_map(function ($adTag) use ($subPublisher, $adNetwork, $site) {
            /** @var AdTagInterface $adTag */
            return new ComparisonReportTypes\AdNetworkDomainAdTagSubPublisher($subPublisher, $adNetwork, $site->getDomain(), $adTag->getPartnerTagId());
        }, $adTags);

        return $this->getReports($reportTypes, $params);
    }

    /**
     * @param array $adTags
     */
    protected function removeDuplicatedPartnerTagId(array &$adTags)
    {
        $partnerTagIds = [];
        /**
         * @var AdTagInterface $adTag
         */
        foreach($adTags as $index => $adTag) {
            if (in_array($adTag->getPartnerTagId(), $partnerTagIds)) {
                unset($adTags[$index]);
                continue;
            }

            $partnerTagIds[] = $adTag->getPartnerTagId();
        }
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
}