<?php


namespace Tagcade\Repository\Report\VideoReport\Hierarchy\Platform;


use Tagcade\Model\Report\VideoReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\VideoReport\Parameter\FilterParameterInterface;

interface VideoPlatformReportRepositoryInterface
{
    /**
     * @param ReportTypeInterface $reportType
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return mixed
     */
    public function getReportsFor(ReportTypeInterface $reportType, \DateTime $startDate, \DateTime $endDate);
}