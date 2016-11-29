<?php


namespace Tagcade\Repository\Report\VideoReport\Hierarchy\Platform;


use Tagcade\Model\Report\VideoReport\Hierarchy\Platform\PlatformReportInterface;

interface VideoPlatformReportRepositoryInterface
{
    /**
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return mixed
     */
    public function getReportsFor(\DateTime $startDate, \DateTime $endDate);

    /**
     * @param PlatformReportInterface $report
     * @return mixed
     */
    public function overrideReport(PlatformReportInterface $report);
}