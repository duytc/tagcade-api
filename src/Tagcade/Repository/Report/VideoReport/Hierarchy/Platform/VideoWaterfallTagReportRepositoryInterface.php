<?php


namespace Tagcade\Repository\Report\VideoReport\Hierarchy\Platform;


use Tagcade\Model\Core\VideoWaterfallTagInterface;

interface VideoWaterfallTagReportRepositoryInterface
{
    public function getReportsFor(VideoWaterfallTagInterface $demandAdTag, \DateTime $startDate, \DateTime $endDate);
}