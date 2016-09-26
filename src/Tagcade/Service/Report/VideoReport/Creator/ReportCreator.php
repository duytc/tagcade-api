<?php

namespace Tagcade\Service\Report\VideoReport\Creator;

use Tagcade\Model\Report\VideoReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\VideoReport\Counter\VideoEventCounterInterface;
use Tagcade\Service\Report\VideoReport\Creator\Creators\CreatorInterface;

class ReportCreator extends ReportCreatorAbstract implements ReportCreatorInterface
{
    /**
     * @param CreatorInterface[] $creators
     * @param VideoEventCounterInterface $eventCounter
     */
    public function __construct(array $creators, VideoEventCounterInterface $eventCounter)
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