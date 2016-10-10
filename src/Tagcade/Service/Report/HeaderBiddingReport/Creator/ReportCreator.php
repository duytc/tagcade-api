<?php

namespace Tagcade\Service\Report\HeaderBiddingReport\Creator;

use Tagcade\Model\Report\HeaderBiddingReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Counter\EventCounterInterface;
use Tagcade\Service\Report\HeaderBiddingReport\Creator\Creators\CreatorInterface;

class ReportCreator extends ReportCreatorAbstract implements ReportCreatorInterface
{
    /**
     * @param CreatorInterface[] $creators
     * @param EventCounterInterface $eventCounter
     */
    public function __construct(array $creators, EventCounterInterface $eventCounter)
    {
        parent::__construct($creators, $eventCounter);
    }

    public function getReport(ReportTypeInterface $reportType)
    {
        $report = parent::getReport($reportType);

        $report->setCalculatedFields();

        return $report;
    }
}