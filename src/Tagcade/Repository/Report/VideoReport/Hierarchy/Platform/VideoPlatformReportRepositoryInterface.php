<?php


namespace Tagcade\Repository\Report\VideoReport\Hierarchy\Platform;


interface VideoPlatformReportRepositoryInterface
{
    /**
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return mixed
     */
    public function getReportsFor(\DateTime $startDate, \DateTime $endDate);
}