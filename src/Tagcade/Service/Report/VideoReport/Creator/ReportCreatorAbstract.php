<?php

namespace Tagcade\Service\Report\VideoReport\Creator;


use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Report\VideoReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\VideoReport\Counter\VideoEventCounterInterface;
use Tagcade\Service\Report\VideoReport\Creator\Creators\CreatorInterface;

abstract class ReportCreatorAbstract
{
    /**
     * @var CreatorInterface[]
     */
    protected $creators;
    /**
     * @var VideoEventCounterInterface
     */
    protected $eventCounter;

    protected $date;

    /* @var boolean */
    protected $dataWithDateHour;

    /**
     * @param CreatorInterface[] $creators
     * @param VideoEventCounterInterface $eventCounter
     */
    public function __construct(array $creators, VideoEventCounterInterface $eventCounter)
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

    public function setDataWithDateHour($dataWithDateHour)
    {
        $this->dataWithDateHour = $dataWithDateHour;
        $this->eventCounter->setDataWithDateHour($dataWithDateHour);

        return $this;
    }

    public function getDataWithDateHour()
    {
        return $this->dataWithDateHour;
    }

    public function getReport(ReportTypeInterface $reportType)
    {
        $creator = $this->getCreatorFor($reportType);

        try {
            $creator->setEventCounter($this->eventCounter);
            $report = $creator->createReport($reportType);
        } catch (\Exception $e) {
            throw new RuntimeException('Could not get the report', $e->getCode(), $e);
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
     * @throws RuntimeException
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