<?php


namespace Tagcade\Repository\Report\VideoReport\Hierarchy\DemandPartner;


use Tagcade\Model\Core\VideoDemandAdTagInterface;
use Tagcade\Repository\Report\VideoReport\Hierarchy\AbstractVideoReportRepository;

class VideoDemandAdTagReportRepository extends AbstractVideoReportRepository implements VideoDemandAdTagReportRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function getReportsFor(VideoDemandAdTagInterface $videoDemandAdTag, \DateTime $startDate, \DateTime $endDate)
    {
        $qb = $this->getReportsByDateRangeQuery($startDate, $endDate)
            ->andWhere('r.videoDemandAdTag = :videoDemandAdTag')
            ->setParameter('videoDemandAdTag', $videoDemandAdTag);

        return $qb->getQuery()->getResult();
    }
}