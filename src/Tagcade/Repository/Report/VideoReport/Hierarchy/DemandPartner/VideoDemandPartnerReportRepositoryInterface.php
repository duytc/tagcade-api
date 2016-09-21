<?php


namespace Tagcade\Repository\Report\VideoReport\Hierarchy\DemandPartner;


use DateTime;
use Tagcade\Model\Core\VideoDemandPartnerInterface;

interface VideoDemandPartnerReportRepositoryInterface
{
    /**
     * get Reports For a video demand partner by a date range
     *
     * @param VideoDemandPartnerInterface $videoDemandPartner
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return mixed
     */
    public function getReportsFor(VideoDemandPartnerInterface $videoDemandPartner, DateTime $startDate, DateTime $endDate);
}