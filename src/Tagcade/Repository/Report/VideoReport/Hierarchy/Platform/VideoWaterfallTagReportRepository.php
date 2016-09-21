<?php


namespace Tagcade\Repository\Report\VideoReport\Hierarchy\Platform;


use Tagcade\Model\Core\VideoWaterfallTagInterface;
use Tagcade\Repository\Report\VideoReport\Hierarchy\AbstractVideoReportRepository;

class VideoWaterfallTagReportRepository extends AbstractVideoReportRepository implements VideoWaterfallTagReportRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function getReportsFor(VideoWaterfallTagInterface $videoWaterfallTag, \DateTime $startDate, \DateTime $endDate)
    {
        $qb = $this->getReportsByDateRangeQuery($startDate, $endDate)
            ->andWhere('r.videoWaterfallTag = :videoWaterfallTag')
            ->setParameter('videoWaterfallTag', $videoWaterfallTag);

        return $qb->getQuery()->getResult();
    }
}