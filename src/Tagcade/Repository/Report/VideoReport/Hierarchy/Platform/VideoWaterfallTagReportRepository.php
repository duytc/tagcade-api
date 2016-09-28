<?php


namespace Tagcade\Repository\Report\VideoReport\Hierarchy\Platform;


use Doctrine\DBAL\Types\Type;
use Tagcade\Model\Core\VideoWaterfallTagInterface;
use Tagcade\Model\User\Role\PublisherInterface;
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

    public function getReportInRangeForPublisher(PublisherInterface $publisher, \DateTime $startDate, \DateTime $endDate)
    {
        $qb = $this->getReportsByDateRangeQuery($startDate, $endDate)
            ->join('r.videoWaterfallTag', 'wtf')
            ->join('wtf.videoPublisher', 'vp')
            ->andWhere('vp.publisher = :publisher')
            ->setParameter('publisher', $publisher);

        return $qb->getQuery()->getResult();
    }

    public function getReportInRangeForAllPublisher(\DateTime $startDate, \DateTime $endDate)
    {
        return $this->getReportsByDateRange($startDate, $endDate);
    }
}