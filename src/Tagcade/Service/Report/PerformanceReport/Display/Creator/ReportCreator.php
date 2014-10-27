<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator;

use DateTime;
use Tagcade\Exception\RuntimeException;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use \Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\CreatorInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Counter\EventCounterInterface;

class ReportCreator implements ReportCreatorInterface
{
    /**
     * @var CreatorInterface[]
     */
    protected $creators;

    /**
     * @var EventCounterInterface
     */
    protected $eventCounter;

    protected $date;

    /**
     * @param CreatorInterface[] $creators
     * @param EventCounterInterface $eventCounter
     */
    public function __construct(array $creators, EventCounterInterface $eventCounter)
    {
        foreach($creators as $creator)
        {
            $this->addCreator($creator);
        }

        $this->eventCounter = $eventCounter;

        $this->setDate(new DateTime('today'));
    }

    /**
     * @inheritdoc
     */
    public function addCreator(CreatorInterface $creator)
    {
        $this->creators[] = $creator;
    }

    /**
     * @inheritdoc
     */
    public function setDate(DateTime $date)
    {
        $this->date = $date;
        $this->eventCounter->setDate($date);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @inheritdoc
     */
    public function getReport(ReportTypeInterface $reportType)
    {
        $creator = $this->getCreatorFor($reportType);

        try {
            $creator->setEventCounter($this->eventCounter);
            $report = $creator->createReport($reportType);
        } catch (\Exception $e) {
            throw new RunTimeException('Could not get the report', $e->getCode(), $e);
        }

        // very important!!!
        // will set off a chain reaction and calculate all fields for the entire report graph
        $report->setCalculatedFields();

        return $report;
    }

    /**
     * @param ReportTypeInterface $reportType
     * @return CreatorInterface
     * @throws RunTimeException
     */
    protected function getCreatorFor(ReportTypeInterface $reportType)
    {
        foreach($this->creators as $creator) {
            if ($creator->supportsReportType($reportType)) {
                return $creator;
            }
        }

        throw new RuntimeException('cannot find a creator for this report type');
    }
}