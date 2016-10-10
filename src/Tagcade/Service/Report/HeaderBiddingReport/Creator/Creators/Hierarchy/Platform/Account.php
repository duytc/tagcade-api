<?php

namespace Tagcade\Service\Report\HeaderBiddingReport\Creator\Creators\Hierarchy\Platform;

use Tagcade\DomainManager\SiteManagerInterface;
use Tagcade\Entity\Report\HeaderBiddingReport\Hierarchy\Platform\AccountReport;
use Tagcade\Model\Report\HeaderBiddingReport\ReportType\Hierarchy\Platform\Account as AccountReportType;
use Tagcade\Model\Report\HeaderBiddingReport\ReportType\Hierarchy\Platform\Site as SiteReportType;
use Tagcade\Model\Report\HeaderBiddingReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\HeaderBiddingReport\Creator\Creators\CreatorAbstract;
use Tagcade\Service\Report\HeaderBiddingReport\Creator\Creators\HasSubReportsTrait;

class Account extends CreatorAbstract implements AccountInterface
{
    use HasSubReportsTrait;

    /** @var SiteManagerInterface */
    protected $siteManager;

    public function __construct(SiteManagerInterface $siteManager, SiteInterface $subReportCreator)
    {
        $this->siteManager = $siteManager;
        $this->subReportCreator = $subReportCreator;
    }

    /**
     * @inheritdoc
     */
    public function doCreateReport(AccountReportType $reportType)
    {
        $this->syncEventCounterForSubReports();

        $report = new AccountReport();

        $publisher = $reportType->getPublisher();

        $report
            ->setPublisher($publisher)
            ->setDate($this->getDate());

        $sites = $this->siteManager->getSitesForPublisher($publisher);

        foreach ($sites as $site) {
            $report->addSubReport(
                $this->subReportCreator->createReport(new SiteReportType($site))
                    ->setSuperReport($report)
            );
        }

        return $report;
    }

    /**
     * @inheritdoc
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof AccountReportType;
    }
}