<?php

namespace Tagcade\Service\Report\HeaderBiddingReport\Selector;

use Tagcade\Bundle\UserBundle\DomainManager\PublisherManagerInterface;
use Tagcade\DomainManager\AdSlotManagerInterface;
use Tagcade\DomainManager\SiteManagerInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\Report\HeaderBiddingReport\ReportType\Hierarchy\Platform as PlatformReportTypes;
use Tagcade\Model\Report\HeaderBiddingReport\ReportType\ReportTypeInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\DateUtilInterface;
use Tagcade\Service\Report\HeaderBiddingReport\Selector\Result\ReportResultInterface;

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
     * @var SiteManagerInterface
     */
    protected $siteManager;

    public function __construct(ReportSelectorInterface $reportSelector, DateUtilInterface $dateUtil, PublisherManagerInterface $userManager, SiteManagerInterface $siteManager)
    {
        $this->reportSelector = $reportSelector;
        $this->dateUtil = $dateUtil;
        $this->userManager = $userManager;
        $this->siteManager = $siteManager;
    }

    /**
     * @inheritdoc
     */
    public function getPlatformReport(Params $params)
    {
        $publishers = $this->userManager->allPublishers();

        return $this->getReports(new PlatformReportTypes\Platform($publishers), $params);
    }

    /**
     * @inheritdoc
     */
    public function getAllPublishersReport(Params $params)
    {
        $publishers = $this->userManager->allPublisherWithHeaderBiddingModule();

        $reportTypes = array_map(function(PublisherInterface $publisher) {
            return new PlatformReportTypes\Account($publisher);
        }, $publishers);

        return $this->getReports($reportTypes, $params);
    }

    /**
     * @inheritdoc
     */
    public function getPublisherReport(PublisherInterface $publisher, Params $params)
    {
        return $this->getReports(new PlatformReportTypes\Account($publisher), $params);
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

    /**
     * @inheritdoc
     */
    public function getSiteReport(SiteInterface $site, Params $params)
    {
        return $this->getReports(new PlatformReportTypes\Site($site), $params);
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