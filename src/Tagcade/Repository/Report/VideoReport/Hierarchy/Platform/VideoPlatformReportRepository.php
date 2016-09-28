<?php


namespace Tagcade\Repository\Report\VideoReport\Hierarchy\Platform;


use Tagcade\Repository\Report\VideoReport\Hierarchy\AbstractVideoReportRepository;

class VideoPlatformReportRepository extends AbstractVideoReportRepository implements VideoPlatformReportRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function getReportsFor(\DateTime $startDate, \DateTime $endDate)
    {
        return $this->getReportsByDateRange($startDate, $endDate);
    }
}