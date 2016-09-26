<?php

namespace Tagcade\Service\Report\VideoReport\Creator\Creators\Hierarchy\Platform;

use Tagcade\Model\Report\VideoReport\Hierarchy\Platform\AccountReportInterface;
use Tagcade\Service\Report\VideoReport\Creator\Creators\CreatorAbstract;
use Tagcade\Entity\Report\VideoReport\Hierarchy\Platform\PlatformReport;
use Tagcade\Model\Report\VideoReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\VideoReport\Creator\Creators\HasSubReportsTrait;

use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\Platform\Platform as PlatformReportType;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\Platform\Account as AccountReportType;

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
            /**@var AccountReportInterface $subReport **/
            $subReport = $this->subReportCreator->createReport(new AccountReportType($publisher));
            $report->addSubReport($subReport->setSuperReport($report));

            $report->setAdTagRequests($report->getAdTagRequests() + $subReport->getAdTagRequests());
            $report->setAdTagBids($report->getAdTagBids() + $subReport->getAdTagBids());
            $report->setAdTagErrors($report->getAdTagErrors() + $subReport->getAdTagErrors());
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