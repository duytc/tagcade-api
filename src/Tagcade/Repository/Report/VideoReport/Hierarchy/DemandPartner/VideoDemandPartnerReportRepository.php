<?php


namespace Tagcade\Repository\Report\VideoReport\Hierarchy\DemandPartner;


use DateTime;
use Tagcade\Model\Core\VideoDemandPartnerInterface;
use Tagcade\Repository\Report\VideoReport\Hierarchy\AbstractVideoReportRepository;

class VideoDemandPartnerReportRepository extends AbstractVideoReportRepository implements VideoDemandPartnerReportRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function getReportsFor(VideoDemandPartnerInterface $videoDemandPartner, DateTime $startDate, DateTime $endDate)
    {
        $qb = $this->getReportsByDateRangeQuery($startDate, $endDate)
            ->andWhere('r.videoDemandPartner = :demandPartner')
            ->setParameter('demandPartner', $videoDemandPartner);

        return $qb->getQuery()->getResult();
    }
}