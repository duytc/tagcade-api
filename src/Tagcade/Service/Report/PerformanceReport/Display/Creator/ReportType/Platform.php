<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator\ReportType;

use Tagcade\Entity\Report\PerformanceReport\Display\PlatformReport;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\ReportType\Behaviours\HasSubReports;

class Platform extends ReportTypeAbstract implements PlatformInterface
{
    use HasSubReports;

    /**
     * @var AccountInterface
     */
    protected $subReportCreator;

    public function __construct(AccountInterface $subReportCreator)
    {
        $this->subReportCreator = $subReportCreator;
    }

    public function doCreateReport(array $publishers)
    {
        $this->syncEventCounterForSubReports();

        $report = new PlatformReport();

        $report
            ->setDate($this->getDate())
        ;

        foreach ($publishers as $publisher) {
            $report->addSubReport(
                $this->subReportCreator->createReport($publisher)
                    ->setSuperReport($report)
            );
        }

        return $report;
    }

    /**
     * @inheritdoc
     */
    public function checkParameter($publishers)
    {
        if (!is_array($publishers)) {
            return false;
        }

        foreach($publishers as $publisher) {
            if (!$publisher instanceof PublisherInterface) {
                return false;
            }
        }

        return true;
    }
}