<?php

namespace Tagcade\Service\Report\RtbReport\Creator;


use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Report\RtbReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\RtbReport\Counter\RtbEventCounterInterface;
use Tagcade\Service\Report\RtbReport\Creator\Creators\RtbCreatorInterface;

abstract class RtbReportCreatorAbstract
{
    /**
     * @var RtbCreatorInterface[]
     */
    protected $creators;
    /**
     * @var RtbEventCounterInterface
     */
    protected $eventCounter;

    /**
     * @var \DateTime
     */
    protected $date;

    /**
     * @param RtbCreatorInterface[] $creators
     * @param RtbEventCounterInterface $eventCounter
     */
    public function __construct(array $creators, RtbEventCounterInterface $eventCounter)
    {
        $this->eventCounter = $eventCounter;

        foreach ($creators as $creator) {
            $this->addCreator($creator);
        }

        $this->setDate(new \DateTime('today'));
    }

    /**
     * get EventCounter
     *
     * @return RtbEventCounterInterface
     */
    public function getEventCounter()
    {
        return $this->eventCounter;
    }

    /**
     * set Date for this creator, also set for eventCounter
     *
     * @param \DateTime $date
     * @return $this
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;
        $this->eventCounter->setDate($date);

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param ReportTypeInterface $reportType
     * @return \Tagcade\Model\Report\RtbReport\ReportInterface|\Tagcade\Model\Report\RtbReport\SubReportInterface
     */
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

    /**
     * add a creator for this reportCreator
     *
     * @param RtbCreatorInterface $creator
     */
    protected function addCreator(RtbCreatorInterface $creator)
    {
        $this->creators[] = $creator;
    }

    /**
     * @param ReportTypeInterface $reportType
     * @return RtbCreatorInterface
     * @throws RunTimeException
     */
    protected function getCreatorFor(ReportTypeInterface $reportType)
    {
        foreach ($this->creators as $creator) {
            if ($creator->supportsReportType($reportType)) {
                return $creator;
            }
        }

        throw new RuntimeException('cannot find a creator for this report type');
    }
}