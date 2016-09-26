<?php


namespace Tagcade\Repository\Report\VideoReport\Hierarchy\Platform;


use Tagcade\Model\Core\VideoPublisherInterface;

interface VideoPublisherReportRepositoryInterface
{
    /**
     * Get report for
     * @param VideoPublisherInterface $publisher
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return mixed
     */
    public function getReportsFor(VideoPublisherInterface $publisher, \DateTime $startDate, \DateTime $endDate);

    /**
     * @param VideoPublisherInterface $publisher
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return mixed
     */
    public function getSumVideoImpressionsForVideoPublisher(VideoPublisherInterface $publisher, \DateTime $startDate, \DateTime $endDate);
}