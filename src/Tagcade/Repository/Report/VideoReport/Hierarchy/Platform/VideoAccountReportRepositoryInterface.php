<?php


namespace Tagcade\Repository\Report\VideoReport\Hierarchy\Platform;


use Tagcade\Model\User\Role\PublisherInterface;

interface VideoAccountReportRepositoryInterface
{
    /**
     * Get report for
     * @param PublisherInterface $publisher
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return mixed
     */
    public function getReportsFor(PublisherInterface $publisher, \DateTime $startDate, \DateTime $endDate);

    /**
     * @param PublisherInterface $publisher
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @return mixed
     */
    public function getSumVideoImpressionsForPublisher(PublisherInterface $publisher, \DateTime $startDate, \DateTime $endDate);

    public function getAggregatedReportsByDateRange(array $publisherIds, \DateTime $startDate, \DateTime $endDate);
}