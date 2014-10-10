<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\ReportType;

use Tagcade\DomainManager\SiteManagerInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Entity\Report\PerformanceReport\Display\AccountReport;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\ReportType\Behaviours\HasSubReports;

class Account extends ReportTypeAbstract implements AccountInterface
{
    use HasSubReports;

    /**
     * @var SiteManagerInterface
     */
    protected $siteManager;

    /**
     * @var SiteInterface
     */
    protected $subReportCreator;

    public function __construct(SiteManagerInterface $siteManager, SiteInterface $subReportCreator)
    {
        $this->siteManager = $siteManager;
        $this->subReportCreator = $subReportCreator;
    }

    /**
     * @inheritdoc
     */
    public function doCreateReport(PublisherInterface $publisher)
    {
        $this->syncEventCounterForSubReports();

        $report = new AccountReport();

        $report
            ->setPublisher($publisher)
            ->setDate($this->getDate())
            ->setName($publisher->getUser()->getUsername())
        ;

        $sites = $this->siteManager->getSitesForPublisher($publisher);

        foreach ($sites as $site) {
            $report->addSubReport(
                $this->subReportCreator->createReport($site, $this->getDate())
                    ->setSuperReport($report)
            );
        }

        return $report;
    }

    /**
     * @inheritdoc
     */
    public function checkParameter($publisher)
    {
        return $publisher instanceof PublisherInterface;
    }
}