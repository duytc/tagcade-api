<?php

namespace Tagcade\Service\Report\RtbReport\Creator;


use Tagcade\Model\Report\RtbReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\RtbReport\Counter\RtbEventCounterInterface;
use Tagcade\Service\Report\RtbReport\Creator\Creators\RtbCreatorInterface;

class RtbReportCreator extends RtbReportCreatorAbstract implements RtbReportCreatorInterface
{
    /**
     * @param RtbCreatorInterface[] $creators
     * @param RtbEventCounterInterface $eventCounter
     */
    public function __construct(array $creators, RtbEventCounterInterface $eventCounter)
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