<?php

namespace Tagcade\Service\Report\UnifiedReport;


use Tagcade\Model\User\Role\PublisherInterface;

interface ReportComparisonCreatorInterface
{
    /**
     * @param PublisherInterface $publisher
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @param $override
     * @return bool
     */
    public function updateComparisonForPublisher(PublisherInterface $publisher, \DateTime $startDate, \DateTime $endDate, $override = false);
}