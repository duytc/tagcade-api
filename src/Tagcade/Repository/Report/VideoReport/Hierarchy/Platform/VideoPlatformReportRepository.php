<?php


namespace Tagcade\Repository\Report\VideoReport\Hierarchy\Platform;


use Tagcade\Model\Report\VideoReport\ReportType\ReportTypeInterface;
use Tagcade\Repository\Report\VideoReport\Hierarchy\AbstractVideoReportRepository;

class VideoPlatformReportRepository extends AbstractVideoReportRepository implements VideoPlatformReportRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function getReportsFor(ReportTypeInterface $reportType, \DateTime $startDate, \DateTime $endDate)
    {
        return $this->getReportsByDateRange($startDate, $endDate);
    }
}