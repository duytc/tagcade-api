<?php


namespace Tagcade\Repository\Report\VideoReport\Hierarchy\Platform;


use Tagcade\Model\Core\VideoDemandAdTagInterface;
use Tagcade\Repository\Report\VideoReport\Hierarchy\AbstractVideoReportRepository;

class VideoDemandAdTagReportRepository extends AbstractVideoReportRepository implements VideoDemandAdTagReportRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function getReportsFor(VideoDemandAdTagInterface $demandAdTag, \DateTime $startDate, \DateTime $endDate)
    {
        $qb = $this->getReportsByDateRangeQuery($startDate, $endDate)
            ->andWhere('r.videoDemandAdTag = :videoDemandAdTag')
            ->setParameter('videoDemandAdTag', $demandAdTag);

        return $qb->getQuery()->getResult();
    }
}