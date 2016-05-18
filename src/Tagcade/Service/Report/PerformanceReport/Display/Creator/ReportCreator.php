<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator;

use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Counter\EventCounterInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\CreatorInterface;

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
        // very important!!!
        // will set off a chain reaction and calculate all fields for the entire report graph
        $report->setCalculatedFields();

        return $report;
    }
}