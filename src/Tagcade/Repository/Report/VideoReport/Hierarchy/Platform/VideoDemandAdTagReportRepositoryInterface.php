<?php


namespace Tagcade\Repository\Report\VideoReport\Hierarchy\Platform;


use Tagcade\Model\Core\VideoDemandAdTagInterface;

interface VideoDemandAdTagReportRepositoryInterface
{
    /**
     * get Reports For a video ad source by a date range
     *
     * @param VideoDemandAdTagInterface $demandAdTag
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return mixed
     */
    public function getReportsFor(VideoDemandAdTagInterface $demandAdTag, \DateTime $startDate, \DateTime $endDate);
}