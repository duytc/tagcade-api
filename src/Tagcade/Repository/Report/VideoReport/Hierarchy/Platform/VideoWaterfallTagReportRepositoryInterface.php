<?php


namespace Tagcade\Repository\Report\VideoReport\Hierarchy\Platform;


use Tagcade\Model\Core\VideoWaterfallTagInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface VideoWaterfallTagReportRepositoryInterface
{
    public function getReportsFor(VideoWaterfallTagInterface $demandAdTag, \DateTime $startDate, \DateTime $endDate);

    public function getReportInRangeForPublisher(PublisherInterface $publisher, \DateTime $startDate, \DateTime $endDate);

    public function getReportInRangeForAllPublisher(\DateTime $startDate, \DateTime $endDate);
}