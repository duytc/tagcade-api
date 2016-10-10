<?php

namespace Tagcade\Service\Report\HeaderBiddingReport\Creator\Creators\Hierarchy\Platform;

use Tagcade\Service\Report\HeaderBiddingReport\Creator\Creators\CreatorAbstract;
use Tagcade\Entity\Report\HeaderBiddingReport\Hierarchy\Platform\PlatformReport;
use Tagcade\Model\Report\HeaderBiddingReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\HeaderBiddingReport\Creator\Creators\HasSubReportsTrait;

use Tagcade\Model\Report\HeaderBiddingReport\ReportType\Hierarchy\Platform\Platform as PlatformReportType;
use Tagcade\Model\Report\HeaderBiddingReport\ReportType\Hierarchy\Platform\Account as AccountReportType;

class Platform extends CreatorAbstract implements PlatformInterface
{
    use HasSubReportsTrait;

    public function __construct(AccountInterface $subReportCreator)
    {
        $this->subReportCreator = $subReportCreator;
    }

    public function doCreateReport(PlatformReportType $reportType)
    {
        $this->syncEventCounterForSubReports();

        $report = new PlatformReport();

        $report
            ->setDate($this->getDate())
        ;

        foreach ($reportType->getPublishers() as $publisher) {
            $report->addSubReport(
                $this->subReportCreator->createReport(new AccountReportType($publisher))
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
        return $reportType instanceof PlatformReportType;
    }
}