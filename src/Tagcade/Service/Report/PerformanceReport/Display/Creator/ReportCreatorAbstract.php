<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Creator;


use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Counter\EventCounterInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Creator\Creators\CreatorInterface;

abstract class ReportCreatorAbstract
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
        $this->eventCounter = $eventCounter;

        foreach($creators as $creator)
        {
            $this->addCreator($creator);
        }

        $this->setDate(new \DateTime('today'));
    }


    public function getEventCounter()
    {
        return $this->eventCounter;
    }

    public function setDate(\DateTime $date)
    {
        $this->date = $date;
        $this->eventCounter->setDate($date);

        return $this;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function getReport(ReportTypeInterface $reportType)
    {
        $creator = $this->getCreatorFor($reportType);

        try {
            $creator->setEventCounter($this->eventCounter);
            $report = $creator->createReport($reportType);
        } catch (\Exception $e) {
            throw new RunTimeException('Could not get the report', $e->getCode(), $e);
        }

        return $report;
    }

    protected function addCreator(CreatorInterface $creator)
    {
        $this->creators[] = $creator;
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