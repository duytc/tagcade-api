<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\Hierarchy\Platform;

use Tagcade\DomainManager\SiteManagerInterface;
use Tagcade\Entity\Report\PerformanceReport\Display\Platform\AccountReport;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform\Account as AccountReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform\Site as SiteReportType;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\CreatorAbstract;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\HasSubReportsTrait;

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
    public function doCreateReport(ReportTypeInterface $reportType)
    {
        $this->syncEventCounterForSubReports();

        /** @var AccountReportType $reportType */
        $accountReport = new AccountReport();

        $publisher = $reportType->getPublisher();

        $accountReport
            ->setPublisher($publisher)
            ->setDate($this->getDate());

        $sites = $this->siteManager->getSitesForPublisher($publisher);

        foreach ($sites as $site) {
            $accountReport->addSubReport(
                $this->subReportCreator->createReport(new SiteReportType($site))
                    ->setSuperReport($accountReport)
            );
        }

        return $accountReport;
    }

    /**
     * @inheritdoc
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof AccountReportType;
    }
}