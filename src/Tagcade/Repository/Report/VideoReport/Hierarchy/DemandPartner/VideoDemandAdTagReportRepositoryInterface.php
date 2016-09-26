<?php


namespace Tagcade\Repository\Report\VideoReport\Hierarchy\DemandPartner;


use Tagcade\Model\Core\VideoDemandAdTagInterface;

interface VideoDemandAdTagReportRepositoryInterface
{
    /**
     * get Reports For a video ad source by a date range
     *
     * @param VideoDemandAdTagInterface $videoAdSource
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return mixed
     */
    public function getReportsFor(VideoDemandAdTagInterface $videoAdSource, \DateTime $startDate, \DateTime $endDate);
}